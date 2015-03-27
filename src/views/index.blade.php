<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        html, body, .lfm-override.container, .lfm-override .col-md-2, .lfm-override .col-md-10 {
            height: 100%;
        }

        .lfm-override *:before, .lfm-override *:after {
            display: none;
        }

        .lfm-override.container:before {
            content: '';
            display: block;
            float: left;
            height: 100%;
        }

        .lfm-override .fill {
            position: relative;
        }

        .lfm-override .fill:after {
            content: '';
            display: block;
            clear: left;
        }

        .lfm-override .wrapper {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .lfm-override .col-md-10 {

        }

        .lfm-override .col-md-2 {
            float: left;
            position: static;
        }

        .lfm-override .col-md-10 {
            position: static;
            overflow: auto;
        }
        
        #lfm-leftcol {
            border-right: 1px solid silver;
        }
    </style>
</head>
<body>
<div class="container lfm-override">
    <div class="row">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Laravel FileManager</h3>
            </div>
            <div class="panel-body">
                <div class="row fill">
                    <div class="wrapper">
                        <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2 left-nav" id="lfm-leftcol">
                            col
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10 right-nav">
                            content
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>