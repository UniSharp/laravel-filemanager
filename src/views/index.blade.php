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
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="nav">
    <a class="navbar-brand invisible" id="to-previous">
      <i class="fa fa-arrow-left fa-fw"></i>
      <span class="d-none d-md-inline">{{ trans('laravel-filemanager::lfm.nav-back') }}</span>
    </a>
    <a class="navbar-brand visible" id="show_tree">
      <i class="fa fa-bars fa-fw"></i>
    </a>
    <a class="navbar-brand">{{ trans('laravel-filemanager::lfm.title-panel') }}</a>
    <a class="navbar-toggler collapsed border-0 p-2 m-0 ml-auto" data-toggle="collapse" data-target="#nav-buttons">
      <i class="fa fa-cog fa-fw"></i>
    </a>
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
        <li class="nav-item">
          <a class="nav-link" data-display="grid">
            <i class="fa fa-th-large fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-thumbnails') }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-display="list">
            <i class="fa fa-list-ul fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-list') }}</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-sort fa-fw"></i>{{ trans('laravel-filemanager::lfm.nav-sort') }}
          </a>
          <div class="dropdown-menu dropdown-menu-right"></div>
        </li>
      </ul>
    </div>
  </nav>

  <aside id="mobile_tree">
    <div class="mt-3 mx-3">
      <h1 style="font-size: 1.5rem;">Laravel File Manager</h1>
      <small class="d-block">Ver 2.0</small>
      <div class="row mt-3">
        <div class="col-3">
          <img src="https://www.unisharp.com/img/favicon_unisharp_logo.png">
        </div>

        <div class="col-9">
          <p>Current usage :</p>
          <p>20 GB (Max : 1 TB)</p>
        </div>
      </div>
      <div class="progress mt-3" style="height: .5rem;">
        <div class="progress-bar progress-bar-striped progress-bar-animated w-75 bg-main" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
      </div>
    </div>
    <ul class="nav nav-pills flex-column mt-3">
      <li class="nav-item active">
        <a class="nav-link" href="#">
          <i class="fa fa-folder-open fa-fw"></i> My
        </a>
      </li>
      <li class="nav-item sub-item">
        <a class="nav-link" href="#">
          <i class="fa fa-folder fa-fw"></i> Share
        </a>
      </li>
      <li class="nav-item sub-item">
        <a class="nav-link" href="#">
          <i class="fa fa-folder fa-fw"></i> Share
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">
          <i class="fa fa-folder fa-fw"></i> Share
        </a>
      </li>
    </ul>
  </aside>

  <div class="container-fluid pt-3">
    <div class="row">
      <div class="col-md-2 d-none d-md-block" id="tree"></div>

      <div class="col-md-10 col-12" id="main">
        {{-- <div class="visible-xs" id="current_dir" style="padding: 5px 15px;background-color: #f8f8f8;color: #5e5e5e;"></div> --}}

        <div id="alerts"></div>

        <div id="empty" class="alert alert-warning d-none">
          <i class="fa fa-folder-open-o"></i> {{ trans('laravel-filemanager::lfm.message-empty') }}
        </div>

        <div id="content"></div>

        <a id="item-template" class="d-none">
          <div class="square"></div>

          <div class="info">
            <div class="item_name text-truncate"></div>
            <time class="text-muted font-weight-light text-truncate"></time>
          </div>
        </a>

        <div id="editor"></div>
      </div>

      <ul id="fab"></ul>
    </div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">{{ trans('laravel-filemanager::lfm.title-upload') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data'>
            <div class="form-group" id="attachment">
              <label for='upload' class='control-label'>{{ trans('laravel-filemanager::lfm.message-choose') }}</label>
              <div class="controls">
                <div class="input-group w-100">
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
          <button type="button" class="btn btn-secondary btn-lg w-50" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary btn-lg w-50" id="upload-btn">{{ trans('laravel-filemanager::lfm.btn-upload') }}</button>
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
