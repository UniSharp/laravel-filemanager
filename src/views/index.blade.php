<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{ Lang::get('laravel-filemanager::lfm.title-page') }}</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
</head>
<body>
  <div class="container-fluid">
    <div class="panel panel-primary" id="wrapper">
      <div class="panel-heading">
        <h3 class="panel-title">{{ Lang::get('laravel-filemanager::lfm.title-panel') }}</h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-xs-2">
            <div id="tree"></div>
          </div>

          <div class="col-xs-10" id="main">
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
                      <i class="fa fa-arrow-left"></i> {{ Lang::get('laravel-filemanager::lfm.nav-back') }}
                    </a>
                  </li>
                  <li><a style='cursor:default;'>|</a></li>
                  <li>
                    <a href="#" id="add-folder">
                      <i class="fa fa-plus"></i> {{ Lang::get('laravel-filemanager::lfm.nav-new') }}
                    </a>
                  </li>
                  <li>
                    <a href="#" id="upload" data-toggle="modal" data-target="#uploadModal">
                      <i class="fa fa-upload"></i> {{ Lang::get('laravel-filemanager::lfm.nav-upload') }}
                    </a>
                  </li>
                  <li><a style='cursor:default;'>|</a></li>
                  <li>
                    <a href="#" id="thumbnail-display">
                      <i class="fa fa-picture-o"></i> {{ Lang::get('laravel-filemanager::lfm.nav-thumbnails') }}
                    </a>
                  </li>
                  <li>
                    <a href="#" id="list-display">
                      <i class="fa fa-list"></i> {{ Lang::get('laravel-filemanager::lfm.nav-list') }}
                    </a>
                  </li>
                </ul>
              </div>
            </nav>

            <div id="alerts">
              @if($no_extension)
              <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> {{ Lang::get('laravel-filemanager::lfm.message-extension_not_found') }}</div>
              @endif
              @if(!is_null($config_error))
              <div class="alert alert-danger"><i class="fa fa-times-circle"></i> {{ $config_error }}</div>
              @endif

              @if (isset($errors) && $errors->any())
                <div class="alert alert-danger" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
            </div>

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
          <h4 class="modal-title" id="myModalLabel">{{ Lang::get('laravel-filemanager::lfm.title-upload') }}</h4>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data'>
            <div class="form-group" id="attachment">
              <label for='upload' class='control-label'>{{ Lang::get('laravel-filemanager::lfm.message-choose') }}</label>
              <div class="controls">
                <div class="input-group" style="width: 100%">
                  <input type="file" id="upload" name="upload[]" multiple="multiple">
                </div>
              </div>
            </div>
            <input type='hidden' name='working_dir' id='working_dir' value='{{$working_dir}}'>
            <input type='hidden' name='show_list' id='show_list' value='{{ ($startup_view == 'list') ? 1 : 0 }}'>
            <input type='hidden' name='type' id='type' value='{{$file_type}}'>
            <input type='hidden' name='_token' value='{{csrf_token()}}'>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">{{ Lang::get('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary" id="upload-btn">{{ Lang::get('laravel-filemanager::lfm.btn-upload') }}</button>
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
    var success_response = "{{ $success_response }}";
    var lfm_route = "{{ $lfm_route }}";
    var lang = {!! json_encode($lang) !!};
  </script>
  <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script>
</body>
</html>
