/*
 * QtWebKit-powered headless test runner using PhantomJS
 *
 * PhantomJS binaries: http://phantomjs.org/download.html
 * Requires PhantomJS 1.6+ (1.7+ recommended)
 *
 * Run with:
 *   phantomjs runner.js [url-of-your-qunit-testsuite]
 *
 * e.g.
 *   phantomjs runner.js http://localhost/qunit/test/index.html
 */

/*global phantom:false, require:false, console:false, window:false, QUnit:false */

(function() {
    'use strict';

    var url, page, timeout,
        args = require('system').args;

    // arg[0]: scriptName, args[1...]: arguments
    if (args.length < 2 || args.length > 3) {
        console.error('Usage:\n  phantomjs runner.js [url-of-your-qunit-testsuite] [timeout-in-seconds]');
        phantom.exit(1);
    }

    url = args[1];
    page = require('webpage').create();
    if (args[2] !== undefined) {
        timeout = parseInt(args[2], 10);
    }

    // Route `console.log()` calls from within the Page context to the main Phantom context (i.e. current `this`)
    page.onConsoleMessage = function(msg) {
        console.log(msg);
    };

    page.onInitialized = function() {
        page.evaluate(addLogging);
    };

    page.onCallback = function(message) {
        var result,
            failed;

        if (message) {
            if (message.name === 'QUnit.done') {
                result = message.data;
                failed = !result || !result.total || result.failed;

                if (!result.total) {
                    console.error('No tests were executed. Are you loading tests asynchronously?');
                }

                // Work-around to avoid "Unsafe JavaScript attempt to access frame" warning in PhantomJS 1.9.8.
                // See: https://github.com/ariya/phantomjs/issues/12697
                page.close();
                setTimeout(function () { phantom.exit(failed ? 1 : 0) }, 0);
            }
            else if (message.name == 'Blanket.done') {
                console.log('Saving coverage data to data.lcov.');

                var fs = require('fs');
                var f = fs.open('data.lcov', 'w');
                f.write(message.data);
                f.close();
            }

        }
    };

    page.open(url, function(status) {
        if (status !== 'success') {
            console.error('Unable to access network: ' + status);
            phantom.exit(1);
        } else {
            // Cannot do this verification with the 'DOMContentLoaded' handler because it
            // will be too late to attach it if a page does not have any script tags.
            var qunitMissing = page.evaluate(function() { return (typeof QUnit === 'undefined' || !QUnit); });
            if (qunitMissing) {
                console.error('The `QUnit` object is not present on this page.');
                phantom.exit(1);
            }

            // Set a timeout on the test running, otherwise tests with async problems will hang forever
            if (typeof timeout === 'number') {
                setTimeout(function() {
                    console.error('The specified timeout of ' + timeout + ' seconds has expired. Aborting...');
                    phantom.exit(1);
                }, timeout * 1000);
            }

            // Do nothing... the callback mechanism will handle everything!
        }
    });

    function addLogging() {
        window.document.addEventListener('DOMContentLoaded', function() {
            var currentTestAssertions = [];

            QUnit.log(function(details) {
                var response;

                // Ignore passing assertions
                if (details.result) {
                    return;
                }

                response = details.message || '';

                if (typeof details.expected !== 'undefined') {
                    if (response) {
                        response += ', ';
                    }

                    response += 'expected: ' + details.expected + ', but was: ' + details.actual;
                }

                if (details.source) {
                    response += "\n" + details.source;
                }

                currentTestAssertions.push('Failed assertion: ' + response);
            });

            QUnit.testDone(function(result) {
                var i,
                    len,
                    name = result.module + ': ' + result.name;

                if (result.failed) {
                    console.log('Test failed: ' + name);

                    for (i = 0, len = currentTestAssertions.length; i < len; i++) {
                        console.log('    ' + currentTestAssertions[i]);
                    }
                }

                currentTestAssertions.length = 0;
            });

            QUnit.done(function(result) {
                console.log('Took ' + result.runtime +  'ms to run ' + result.total + ' tests. ' + result.passed + ' passed, ' + result.failed + ' failed.');

                if (typeof window.callPhantom === 'function') {
                    window.callPhantom({
                        'name': 'QUnit.done',
                        'data': result
                    });
                }
            });

            blanket.options(
                'reporter',
                function(coverage) {
                    var result = '';

                    function addLine() {
                        for (var i=0; i<arguments.length; i++) {
                            result += arguments[i];
                        }

                        result += '\n';
                    }

                    for (var filename in coverage.files) {
                        var data = coverage.files[filename];

                        addLine('SF:', filename.replace('http://localhost:8000/', ''));

                        data.source.forEach(function(line, num) {
                            num++;

                            if (data[num] !== undefined) {
                                addLine('DA:', num, ',', data[num]);
                            }
                        });

                        addLine('end_of_record');
                    }

                    if (typeof window.callPhantom === 'function') {
                        window.callPhantom({
                            'name': 'Blanket.done',
                            'data': result
                        });
                    }
                }
            );
        }, false);
    }
})();
