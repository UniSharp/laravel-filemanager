<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <style>
        html,body{
            height:100%;
        }
        .container {
            height:100%;
            margin-left: 5px;
            margin-right: 5px;
            width: 99%;
        }

        .fill{
            height:100%;
            min-height:100%;
        }

        .wrapper {
            height: 100%;
        }

        #lfm-leftcol {
            min-height: 80%;
        }

        #right-nav {
            border-left: 1px solid silver;
            height: 90%;
            min-height: 90%;
        }

        .highlight {
            background: red;
        }

        #content {
            overflow: auto;
        }

        #tree1 {
            margin-left: 5px;
        }

        .pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row fill">
        <div class="panel panel-primary fill">
            <div class="panel-heading">
                <h3 class="panel-title">Laravel FileManager</h3>
            </div>
            <div class="panel-body fill">
                <div class="row fill">
                    <div class="wrapper fill">
                        <div class="col-md-2 col-lg-2 col-sm-2 col-xs-2 left-nav fill" id="lfm-leftcol">
                            <div id="tree1" data-url="/laravel-filemanager/data">
                            </div>
                            <div id="folder-options">
                                <a class="btn btn-primary btn-xs pointer add-folder" id="add-folder"><i class="fa fa-plus"></i> New</a>
                                <a id="delete-folder" class="btn btn-primary btn-xs pointer delete-folder"><i class="fa fa-remove"></i> Delete</a>
                            </div>
                        </div>
                        <div class="col-md-10 col-lg-10 col-sm-10 col-xs-10 right-nav" id="right-nav">
                            <nav class="navbar navbar-default">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                                                data-target="#bs-example-navbar-collapse-1">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                    </div>
                                    <div class="collapse navbar-collapse">
                                        <ul class="nav navbar-nav">
                                            <li><a href="#!" id="upload" data-toggle="modal" data-target="#uploadModal"><i
                                                            class="fa fa-upload"></i> Upload</a></li>
                                            <li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"
                                                   aria-expanded="false">Edit <span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="#!" onclick="crop()"><i class="fa fa-crop"></i> Crop</a></li>
                                                    <li><a href="#!" onclick="scale()"><i class="fa fa-arrows-v"></i> Scale</a></li>
                                                    <li><a href="#!" onclick="rotate()"><i class="fa fa-rotate-right"></i> Rotate</a></li>
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
                            <div id="content" class="row">

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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Upload File</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(array('url' => '/laravel-filemanager/upload', 'role' => 'form', 'name' => 'uploadForm',
                'id' => 'uploadForm', 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
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
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.3.0/bootbox.js"></script>
<script>
    $(document).ready(function () {
        // load folders
        $.ajax({
            type: "GET",
            dataType: "text",
            url: "/laravel-filemanager/data",
            data: "base={{ $working_dir }}",
            cache: false
        }).done(function (data) {
            $("#tree1").html(data);
        });
        loadImages();

        @if (Input::has('base'))

        @endif
    });

    function highlight(x){
        $(".thumbnail-img").not('#' + x).removeClass('highlight');
        if ($("#" + x).hasClass('highlight')){
            $("#" + x).removeClass('highlight');
        } else {
            $("#" + x).addClass('highlight');
        }
    }

    $("#upload-btn").click(function () {
        $("#upload-btn").html('<i class="fa fa-refresh fa-spin"></i> Uploading...');
        $("#uploadForm").submit();
    });

    function clickFolder(x,y){
        $('.folder-item').addClass('fa-folder');
        $('.folder-item').not("#folder_top > i").removeClass('fa-folder-open');
        if (y == 0) {
            if ($('#' + x + ' > i').hasClass('fa-folder')) {
                $('#' + x + ' > i').not("#folder_top > i").removeClass('fa-folder');
                $('#' + x + ' > i').not("#folder_top > i").addClass('fa-folder-open');
            } else {
                $('#' + x + ' > i').removeClass('fa-folder-open');
                $('#' + x + ' > i').addClass('fa-folder');
            }
        }
        $("#working_dir").val($('#' + x).data('id'));
        loadImages();
    }

    function loadImages(){
        $.ajax({
            type: "GET",
            dataType: "text",
            url: "/laravel-filemanager/picsjson",
            data: "base=" + $("#working_dir").val(),
            cache: false
        }).done(function (data) {
            $("#content").html(data);
        });
    }

    function trash() {
        if ($(".highlight").length > 0) {
            bootbox.confirm("Are you sure you want to delete the "
                        + $(".highlight").length
                        + " selected image(s)?", function (result) {
                if (result == true) {
                    var toDelete = [];
                    $(".highlight").each(function () {
                        toDelete.push($(this).data('id'));
                    })
                    window.location.href = '/laravel-filemanager/delete?'
                    + 'base='
                    + $("#working_dir").val()
                    + '&items='
                    + JSON.stringify(toDelete);
                }
            });
        }
    }

    function crop(){
        var theImageId = $('.highlight img').map(function(){
            return this.id;
        }).get();
        alert(theImageId);
    }

    function scale(){
        var theImageId = $('.highlight img').map(function(){
            return this.id;
        }).get();
        alert(theImageId);
    }

    function rotate(){
        var theImageId = $('.highlight img').map(function(){
            return this.id;
        }).get();
        alert(theImageId);
    }

    $("#add-folder").click(function(){
        bootbox.prompt("Folder name:", function(result) {
            if (result === null) {
            } else {
                location.href='/laravel-filemanager/newfolder?name=' + result;
            }
        });
    });

    $("#delete-folder").click(function(){
        if ($(".fa-folder-open").not("#folder_top > i").length > 0) {
            bootbox.confirm("Are you sure you want to delete the folder "
            + $(".fa-folder-open").not("#folder_top > i").data('id')
            + " and all of its contents?", function (result) {
                if (result == true) {
                    window.location.href = '/laravel-filemanager/deletefolder?'
                    + 'name='
                    + $(".fa-folder-open").not("#folder_top > i").data('id');
                }
            });
        }
    });

    function useFile(file){
        var path = $('#working_dir').val();
        //console.log(path);
        function getUrlParam( paramName ) {
            var reParam = new RegExp( '(?:[\?&]|&)' + paramName + '=([^&]+)', 'i' ) ;
            var match = window.location.search.match(reParam) ;

            return ( match && match.length > 1 ) ? match[ 1 ] : null ;
        }
        var funcNum = getUrlParam( 'CKEditorFuncNum' );
        window.opener.CKEDITOR.tools.callFunction( funcNum, path + "/" + file );

        if (path != '/') {
            //alert('{{ \Config::get('lfm.images_url') }}' + path + "/" + file);
            window.opener.CKEDITOR.tools.callFunction(funcNum, '{{ \Config::get('lfm.images_url') }}' + path + "/" + file );
        } else {
            //alert('{{ \Config::get('lfm.images_url') }}' + file);
            window.opener.CKEDITOR.tools.callFunction( funcNum, '{{ \Config::get('lfm.images_url') }}' + file );
        }
        window.close();
    }

</script>
</body>
</html>
