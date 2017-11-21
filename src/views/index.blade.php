<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Chrome, Firefox OS and Opera -->
  <meta name="theme-color" content="#75C7C3">
  <!-- Windows Phone -->
  <meta name="msapplication-navbutton-color" content="#75C7C3">
  <!-- iOS Safari -->
  <meta name="apple-mobile-web-app-status-bar-style" content="#75C7C3">

  <title>{{ trans('laravel-filemanager::lfm.title-page') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('vendor/laravel-filemanager/img/folder.png') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <style>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/css/lfm.css')) !!}</style>
  {{-- Use the line below instead of the above if you need to cache the css. --}}
  {{-- <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}"> --}}
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/mfb.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark" id="nav">
    {{-- <div class="navbar-header">
      <a class="navbar-toggle collapsed" data-toggle="collapse" data-target="#nav-buttons">
        <i class="fa fa-cog fa-2x fa-tw"></i>
      </a>
      <a class="navbar-brand invisible hidden-xs" id="to-previous">
        <i class="fa fa-arrow-left fa-fw"></i>
        <span class="hidden-xs">{{ trans('laravel-filemanager::lfm.nav-back') }}</span>
      </a>
      <a class="navbar-brand visible-xs" id="show_tree">
        <i class="fa fa-bars fa-fw"></i>
      </a>
      <a class="navbar-brand">{{ trans('laravel-filemanager::lfm.title-panel') }}</a>
    </div> --}}
    <a class="navbar-brand">{{ trans('laravel-filemanager::lfm.title-panel') }}</a>
    <div class="collapse navbar-collapse" id="nav-buttons">
      <ul class="navbar-nav ml-auto">
        <li id="loading" class="nav-item">
          <a class="nav-link">
            <i class="fa fa-spinner fa-spin"></i>
          </a>
        </li>
        {{-- <li>
          <a id="multi_selection_toggle">
            <i class="fa fa-check-square fa-fw"></i>
            <span>Multi selection</span>
          </a>
        </li> --}}
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-sort fa-fw"></i>{{ trans('laravel-filemanager::lfm.nav-sort') }}
          </a>
          <div class="dropdown-menu"></div>
        </li>
      </ul>
    </div>
  </nav>

  <div class="container-fluid">
    <aside id="mobile_tree">
      <div class="row">
        <div class="col-xs-4">
          <img src="/vendor/laravel-filemanager/img/folder.png" style="width: 100%">
        </div>
        <div class="col-xs-8">
          <h4>Laravel File Manager</h4>
          <small>Ver 2.0</small>
        </div>
      </div>
      <ul class="nav nav-pills nav-stacked">
        <li class="active"><a href="#">
          <i class="fa fa-user fa-tw"></i> My</a>
        </li>
        <li><a href="#">
          <i class="fa fa-share-alt fa-tw"></i> Share</a>
        </li>
      </ul>
    </aside>


    <div class="row">
      <div class="col-sm-2 hidden-xs" id="tree"></div>

      <div class="col-sm-10 col-xs-12" id="main">
        {{-- <div class="visible-xs" id="current_dir" style="padding: 5px 15px;background-color: #f8f8f8;color: #5e5e5e;"></div> --}}

        <div id="alerts"></div>

        <div id="content">
          <ul id="items" class="list-unstyled"></ul>

          <li id="item-template" class="hide">
            <a>
              <div class="square"></div>

              <div>
                <div class="item_name"></div>
                <time></time>
              </div>
            </a>
          </li>

          <div class="alert alert-warning hide" id="empty">
            <i class="fa fa-folder-open-o"></i> {{ trans('laravel-filemanager::lfm.message-empty') }}
          </div>
        </div>

        <div id="editor"></div>
      </div>

      <ul id="fab"></ul>
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

  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/cropper.min.js') }}"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/jquery.form.min.js') }}"></script>
  <script>
    var route_prefix = "{{ url('/') }}";
    var lfm_route = "{{ url(config('lfm.url_prefix')) }}";
    var lang = {!! json_encode(trans('laravel-filemanager::lfm')) !!};
    var actions = [
      {
        name: 'use',
        icon: 'check',
        label: 'Confirm'
      },
      {
        name: 'rename',
        icon: 'edit',
        label: lang['menu-rename']
      },
      {
        name: 'download',
        icon: 'arrow-circle-o-down',
        label: lang['menu-download']
      },
      {
        name: 'preview',
        icon: 'image',
        label: lang['menu-view']
      },
      {
        name: 'resize',
        icon: 'arrows-alt',
        label: lang['menu-resize']
      },
      {
        name: 'crop',
        icon: 'crop',
        label: lang['menu-crop']
      },
      {
        name: 'trash',
        icon: 'trash',
        label: lang['menu-delete']
      },
      {
        name: 'grid',
        icon: 'th-large',
        label: lang['nav-thumbnails']
      },
      {
        name: 'list',
        icon: 'list-ul',
        label: lang['nav-list']
      },
    ];

    var sortings = [
      {
        by: 'alphabetic',
        icon: 'sort-alpha-asc',
        label: lang['nav-sort-alphabetic']
      },
      {
        by: 'time',
        icon: 'sort-amount-asc',
        label: lang['nav-sort-time']
      }
    ];
  </script>
  <script>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/script.js')) !!}</script>
  {{-- Use the line below instead of the above if you need to cache the script. --}}
  {{-- <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script> --}}
</body>
</html>
