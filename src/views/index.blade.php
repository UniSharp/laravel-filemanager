<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/vendor/laravel-filemanager/tree-jquery/jqtree.css" />
    <style>
       .wrapper {
           min-height: 500px;
       }
        #lfm-leftcol {
            border-right: 1px solid silver;
            min-height: 500px;
        }
        .highlight {
            background: red;
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
                            <div id="tree1" data-url="/laravel-filemanager/data">
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10 right-nav">
                            <nav class="navbar navbar-default">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>
                                    <a class="navbar-brand" href="#">LFM</a>
                                    <!-- Collect the nav links, forms, and other content for toggling -->
                                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                                        <ul class="nav navbar-nav">
                                            <li><a href="#!" id="upload" data-toggle="modal" data-target="#uploadModal"><i class="fa fa-upload"></i> Upload</a></li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Edit <span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#!"><i class="fa fa-crop"></i> Crop</a></li>
                                                    <li><a href="#!"><i class="fa fa-arrows-v"></i> Scale</a></li>
                                                    <li><a href="#!"><i class="fa fa-rotate-right"></i> Rotate</a></li>
                                                    <li><a href="#!" onclick="trash()"><i class="fa fa-trash"></i> Delete</a></li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="#!" onclick="trash()">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>
                            @if ($errors->any())
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger" role="alert">
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div id="content" class="row" style="overflow: auto">
                                @foreach($files as $file)
                                    <div class="col-sm-6 col-md-2">
                                        <a href="#" class="thumbnail" data-id="{{ basename($file) }}">
                                            <img src="{{ $base }}thumbs/{{ basename($file) }}">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload File</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '/laravel-filemanager/upload', 'role' => 'form', 'name' => 'uploadForm', 'id' => 'uploadForm', 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
                <div class="form-group" id="attachment">
                    {!! Form::label('file_to_upload', 'Choose File', array('class' => 'control-label')); !!}
                    <div class="controls">
                        <div class="input-group" style="width: 100%">
                            <input type="file" name="file_to_upload">
                        </div>
                    </div>
                </div>
                {!! Form::hidden('working_dir', $working_dir, ['id' => 'working_dir']) !!}
                {!! Form::close() !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="upload-btn">Upload File</button>
            </div>
        </div>
    </div>
</div>

<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script src="/vendor/laravel-filemanager/tree-jquery/tree.jquery.js"></script>
<script src="/vendor/laravel-filemanager/jquery-cookie/jquery.cookie.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.3.0/bootbox.js"></script>
<script>
    $(document).ready(function () {
        $('#tree1').tree({
            saveState: 'my-tree',
            closedIcon: $('<i class="fa fa-folder"></i>'),
            openedIcon: $('<i class="fa fa-folder-open"></i>')
        });

        $('#tree1').bind(
                'tree.click',
                function(event) {
                    // The clicked node is 'event.node'
                    var thisNode = event.node;
                    var parent_node = thisNode.parent;
                    if (thisNode.getLevel() == 1) {
                        if (thisNode.children.length > 0) {
                            location.href = '/laravel-filemanager?base=' + thisNode.name;
                        } else {
                            if (window.location.href.toString().split(window.location.host)[1] != '/laravel-filemanager') {
                                location.href = '/laravel-filemanager';
                            }
                        }
                    } else {
                        location.href = '/laravel-filemanager?base=' + parent_node.name;
                    }
                }
        );

        $('#tree1').bind(
                'tree.open',
                function(e) {
                    $("#working_dir").val(e.node.name);
                }
        );
    });

    $("#upload-btn").click(function(){
       $("#uploadForm").submit();
    });

    $(".thumbnail").click(function(){
        if ($(this).hasClass('highlight'))
        {
            $(this).removeClass('highlight');
        }
        else
        {
            $(this).addClass('highlight');
        }
    })

    function trash(){
        if ($(".highlight").length > 0){
            bootbox.confirm("Are you sure you want to delete the "
                    + $(".highlight").length
                    + " selected image(s)?", function(result) {
                if (result==true)
                {
                    var toDelete = [];
                    $(".highlight").each(function(){
                        console.log($(this).data('id'));
                        toDelete.push($(this).data('id'));
                    })
                    window.location.href = '/laravel-filemanager/delete?'
                            + 'base='
                            + '{{ $working_dir }}'
                            + '&items='
                            + JSON.stringify(toDelete);
                }
            });
        }
    }
</script>
</body>
</html>