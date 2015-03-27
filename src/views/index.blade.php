<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <style>
        html, body {
            height: 100%;
        }

        .container, .panel {
            height: 100%;
        }

        .left-nav, .right-nav {
            height: 100%;
        }

        #lfm-leftcol {
            border-right: 1px solid silver;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">Laravel FileManager</h3>
        </div>
        <div class="panel-body">
            <div class="row">
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

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
</body>
</html>