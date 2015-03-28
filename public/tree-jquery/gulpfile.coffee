gulp      = require 'gulp'
coffee    = require 'gulp-coffee'
coffeeify = require 'gulp-coffeeify'
exec      = require('child_process').exec

gulp.task 'jqtree', ->
    gulp.src './src/tree.jquery.coffee'
    .pipe coffeeify()
    .pipe gulp.dest('./')

gulp.task 'lib', ->
    gulp.src './src/*.coffee'
    .pipe coffee(bare: true)
    .pipe gulp.dest('./lib')

gulp.task 'build_test', ->
    gulp.src './src/test.js'
    .pipe coffeeify()
    .pipe gulp.dest('./test')

gulp.task 'jekyll', (cb) ->
    exec 'jekyll build', (err, stdout, stderr) ->
        console.log(stdout)
        console.log(stderr)
        cb(err)

gulp.task 'watch', ['default'], ->
    gulp.watch ['./src/*.coffee', './src/test.js'], ['default']

gulp.task 'default', ['jqtree', 'build_test', 'lib']
