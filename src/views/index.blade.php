<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>File Manager</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/vendor/laravel-filemanager/css/cropper.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
    <style>
        html, body {
            height: 100%;
        }

        .img-row {
            overflow: visible;
        }

        .container {
            height: 100%;
            margin-left: 5px;
            margin-right: 5px;
            width: 99%;
        }

        .fill {
            height: 100%;
            min-height: 100%;
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

        #content {
            overflow: auto;
        }

        #tree1 {
            margin-left: 5px;
        }

        .pointer {
            cursor: pointer;
        }

        .img-preview {
            background-color: #f7f7f7;
            overflow: hidden;
            width: 100%;
            text-align: center;
            height: 200px;
        }

        .hidden {
            display: none;
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
                            <div id="tree1">
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
                                        <ul class="nav navbar-nav" id="nav-buttons">
                                            <li>
                                                <a href="#!" id="upload" data-toggle="modal" data-target="#uploadModal"><i
                                                            class="fa fa-upload"></i> Upload</a>
                                            </li>
                                            <li>
                                                <a href="#!" class="add-folder" id="add-folder"><i
                                                            class="fa fa-plus"></i> New Folder</a>
                                            </li>
                                            <li>
                                                <a href="#!" class="thumbnail-display" id="thumbnail-display"><i
                                                            class="fa fa-picture-o"></i> Thumbnails</a>
                                            </li>
                                            <li>
                                                <a href="#!" class="list-display" id="list-display"><i
                                                            class="fa fa-list"></i> List</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </nav>

                            @if ($errors->any())
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="alert alert-danger" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div id="content" class="row fill">

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
                {!! Form::hidden('show_list', 0, ['id' => 'show_list']) !!}
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
<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="/vendor/laravel-filemanager/js/cropper.min.js"></script>
<script src="/vendor/laravel-filemanager/js/jquery.form.min.js"></script>
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

    function highlight(x) {
        $(".thumbnail-img").not('#' + x).removeClass('highlight');
        if ($("#" + x).hasClass('highlight')) {
            $("#" + x).removeClass('highlight');
        } else {
            $("#" + x).addClass('highlight');
        }
    }

    $("#upload-btn").click(function () {
        $("#upload-btn").html('<i class="fa fa-refresh fa-spin"></i> Uploading...');
        //$("#uploadForm").submit();

        var options = {
            beforeSubmit:  showRequest,  // pre-submit callback
            success:       showResponse  // post-submit callback
        };

        function showRequest(formData, jqForm, options) {
            return true;
        }

        // post-submit callback
        function showResponse(responseText, statusText, xhr, $form)  {
            $("#uploadModal").modal('hide');
            loadImages();
        }

        $("#uploadForm").ajaxSubmit(options);
        return false;
    });

    function clickRoot() {
        $('.folder-item').removeClass('fa-folder-open').addClass('fa-folder');
        $("#working_dir").val('/');
        loadImages();
    }

    function clickFolder(x, y) {
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

    @if ((Session::has('lfm_type')) && (Session::get('lfm_type') == "Images"))
        function loadImages() {
            $.ajax({
                type: "GET",
                dataType: "html",
                url: "/laravel-filemanager/jsonimages",
                data: {
                    base: $("#working_dir").val(),
                    show_list: $("#show_list").val()
                },
                cache: false
            }).done(function (data) {
                $("#content").html(data);
                $("#nav-buttons").removeClass("hidden");
                $(".dropdown-toggle").dropdown();
            });
        }
    @else
        function loadImages() {
        $.ajax({
            type: "GET",
            dataType: "html",
            url: "/laravel-filemanager/jsonfiles",
            data: {
                base: $("#working_dir").val(),
                show_list: $("#show_list").val()
            },
            cache: false
        }).done(function (data) {
            $("#content").html(data);
            $("#nav-buttons").removeClass("hidden");
            $(".dropdown-toggle").dropdown();
        });
    }
    @endif

    function trash(x) {
        bootbox.confirm("Are you sure you want to delete this item?", function (result) {
            if (result == true) {
                $.ajax({
                    type: "GET",
                    dataType: "text",
                    url: "/laravel-filemanager/delete",
                    data: {
                        base: $("#working_dir").val(),
                        items: x
                    },
                    cache: false
                }).done(function (data) {
                    if (data != "OK") {
                        notify(data);
                    } else {
                        loadImages();
                    }
                });
            }
        });
    }

    function cropImage(x) {
        $.ajax({
            type: "GET",
            dataType: "text",
            url: "/laravel-filemanager/crop",
            data: "img="
            + x
            + "&dir=" + $("#working_dir").val(),
            cache: false
        }).done(function (data) {
            $("#nav-buttons").addClass('hidden');
            $("#content").html(data);
        });
    }

    function notImp() {
        bootbox.alert('Not yet implemented!');;
    }

    $(".add-folder").click(function () {
        bootbox.prompt("Folder name:", function (result) {
            if (result === null) {
            } else {
                $.ajax({
                    type: "GET",
                    dataType: "text",
                    url: "/laravel-filemanager/newfolder",
                    data: {
                        name: result
                    },
                    cache: false
                }).done(function (data) {
                    if (data == "OK") {
                        loadImages();
                    } else {
                        notify(data);
                    }
                });
            }
        });
    });


    function useFile(file) {
        var path = $('#working_dir').val();

        function getUrlParam(paramName) {
            var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
            var match = window.location.search.match(reParam);
            return ( match && match.length > 1 ) ? match[1] : null;
        }

        var funcNum = getUrlParam('CKEditorFuncNum');
        window.opener.CKEDITOR.tools.callFunction(funcNum, path + "/" + file);

        if (path != '/') {
            window.opener.CKEDITOR.tools.callFunction(funcNum, '{{ \Config::get('lfm.images_url') }}' + path + "/" + file);
        } else {
            window.opener.CKEDITOR.tools.callFunction(funcNum, '{{ \Config::get('lfm.images_url') }}' + file);
        }
        window.close();
    }

    function rename(x) {
        bootbox.prompt({
            title: "Rename to:",
            value: x,
            callback: function (result) {
                if (result === null) {
                } else {
                    $.ajax({
                        type: "GET",
                        dataType: "text",
                        url: "/laravel-filemanager/rename",
                        data: {
                            file: x,
                            dir: $("#working_dir").val(),
                            new_name: result
                        },
                        cache: false
                    }).done(function (data) {
                        if (data == "OK") {
                            loadImages();
                        } else {
                            notify(data);
                        }
                    });
                }
            }
        });
    }

    function notify(x) {
        bootbox.alert(x);
    }

    function scaleImage(x) {
        $.ajax({
            type: "GET",
            dataType: "text",
            url: "/laravel-filemanager/scale",
            data: "img="
            + x
            + "&dir=" + $("#working_dir").val(),
            cache: false
        }).done(function (data) {
            $("#nav-buttons").addClass('hidden');
            $("#content").html(data);
        });
    }

    $("#thumbnail-display").click(function () {
        $("#show_list").val(0);
        loadImages();
    });

    $("#list-display").click(function () {
        $("#show_list").val(1);
        loadImages();
    });
</script>
</body>
</html>
