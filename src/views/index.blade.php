<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ trans('laravel-filemanager::lfm.title-page') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('vendor/laravel-filemanager/img/folder.png') }}">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
</head>
<body>
  <div class="container-fluid" style="padding:0">
    <div class="panel panel-primary" style="margin:0;height:100vh;border-radius:0" id="wrapper">
      <div class="panel-heading" style="border-radius:0">
        <h1 class="panel-title" style="padding:10px 0 10px 0">{{ trans('laravel-filemanager::lfm.title-panel') }}</h1>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-2 hidden-sm">
            <div id="tree"></div>
          </div>

          <div class="col-md-10 col-sm-12" id="main">
            <nav class="navbar navbar-default">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
              </div>
              <div class="collapse navbar-collapse">
                <ul class="nav navbar-nav" id="nav-buttons">
                  <li>
                    <a href="#" id="to-previous">
                      <i class="fa fa-arrow-left"></i> {{ trans('laravel-filemanager::lfm.nav-back') }}
                    </a>
                  </li>
                  <li><a style='cursor:default;'>|</a></li>
                  <li>
                    <a href="#" id="add-folder">
                      <i class="fa fa-plus"></i> {{ trans('laravel-filemanager::lfm.nav-new') }}
                    </a>
                  </li>
                  <li>
                    <a href="#" id="upload" data-toggle="modal" data-target="#uploadModal">
                      <i class="fa fa-upload"></i> {{ trans('laravel-filemanager::lfm.nav-upload') }}
                    </a>
                  </li>
                  <li><a style='cursor:default;'>|</a></li>
                  <li>
                    <a href="#" id="thumbnail-display">
                      <i class="fa fa-picture-o"></i> {{ trans('laravel-filemanager::lfm.nav-thumbnails') }}
                    </a>
                  </li>
                  <li>
                    <a href="#" id="list-display">
                      <i class="fa fa-list"></i> {{ trans('laravel-filemanager::lfm.nav-list') }}
                    </a>
                  </li>
                </ul>
              </div>
            </nav>

            <div id="alerts"></div>

            <div id="content"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">{{ trans('laravel-filemanager::lfm.title-upload') }}</h4>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data'>
            <div class="form-group" id="attachment">
              <label for='upload' class='control-label'>{{ trans('laravel-filemanager::lfm.message-choose') }}</label>
              <div class="controls">
                <div class="input-group" style="width: 100%">
                  <input type="file" id="upload" name="upload[]" multiple="multiple">
                </div>
              </div>
            </div>
            <input type='hidden' name='working_dir' id='working_dir'>
            <input type='hidden' name='type' id='type' value='{{ request("type") }}'>
            <input type='hidden' name='_token' value='{{csrf_token()}}'>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary" id="upload-btn">{{ trans('laravel-filemanager::lfm.btn-upload') }}</button>
        </div>
      </div>
    </div>
  </div>

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/cropper.min.js') }}"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/jquery.form.min.js') }}"></script>
  <script>
    var route_prefix = "{{ url('/') }}";
    var lfm_route = "{{ url(config('lfm.prefix')) }}";
    var lang = {!! json_encode(trans('laravel-filemanager::lfm')) !!};
  </script>
  <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script>
  {{-- Use the line below instead of the above if you need to ignore browser cache. --}}
  <!-- <script>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/script.js')) !!}</script> -->
</body>
</html>
