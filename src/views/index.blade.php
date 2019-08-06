<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=EDGE" />
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Chrome, Firefox OS and Opera -->
  <meta name="theme-color" content="#333844">
  <!-- Windows Phone -->
  <meta name="msapplication-navbutton-color" content="#333844">
  <!-- iOS Safari -->
  <meta name="apple-mobile-web-app-status-bar-style" content="#333844">

  <title>{{ trans('laravel-filemanager::lfm.title-page') }}</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('vendor/laravel-filemanager/img/72px color.png') }}">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/dropzone.min.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/mime-icons.min.css') }}">
  <style>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/css/lfm.css')) !!}</style>
  {{-- Use the line below instead of the above if you need to cache the css. --}}
  {{-- <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}"> --}}
</head>
<body>
  <nav class="navbar sticky-top navbar-expand-lg navbar-dark" id="nav">
    <a class="navbar-brand invisible-lg d-none d-lg-inline" id="to-previous">
      <i class="fas fa-arrow-left fa-fw"></i>
      <span class="d-none d-lg-inline">{{ trans('laravel-filemanager::lfm.nav-back') }}</span>
    </a>
    <a class="navbar-brand d-block d-lg-none" id="show_tree">
      <i class="fas fa-bars fa-fw"></i>
    </a>
    <a class="navbar-brand d-block d-lg-none" id="current_folder"></a>
    <a id="loading" class="navbar-brand"><i class="fas fa-spinner fa-spin"></i></a>
    <div class="ml-auto px-2">
      <a class="navbar-link d-none" id="multi_selection_toggle">
        <i class="fa fa-check-double fa-fw"></i>
        <span class="d-none d-lg-inline">{{ trans('laravel-filemanager::lfm.menu-multiple') }}</span>
      </a>
    </div>
    <a class="navbar-toggler collapsed border-0 px-1 py-2 m-0" data-toggle="collapse" data-target="#nav-buttons">
      <i class="fas fa-cog fa-fw"></i>
    </a>
    <div class="collapse navbar-collapse flex-grow-0" id="nav-buttons">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" data-display="grid">
            <i class="fas fa-th-large fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-thumbnails') }}</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-display="list">
            <i class="fas fa-list-ul fa-fw"></i>
            <span>{{ trans('laravel-filemanager::lfm.nav-list') }}</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-sort fa-fw"></i>{{ trans('laravel-filemanager::lfm.nav-sort') }}
          </a>
          <div class="dropdown-menu dropdown-menu-right border-0"></div>
        </li>
      </ul>
    </div>
  </nav>

  <nav class="bg-light fixed-bottom border-top d-none" id="actions">
    <a data-action="open" data-multiple="false"><i class="fas fa-folder-open"></i>{{ trans('laravel-filemanager::lfm.btn-open') }}</a>
    <a data-action="preview" data-multiple="true"><i class="fas fa-images"></i>{{ trans('laravel-filemanager::lfm.menu-view') }}</a>
    <a data-action="use" data-multiple="true"><i class="fas fa-check"></i>{{ trans('laravel-filemanager::lfm.btn-confirm') }}</a>
  </nav>

  <div class="d-flex flex-row">
    <div id="tree"></div>

    <div id="main">
      <div id="alerts"></div>

      <nav aria-label="breadcrumb" class="d-none d-lg-block" id="breadcrumbs">
        <ol class="breadcrumb">
          <li class="breadcrumb-item invisible">Home</li>
        </ol>
      </nav>

      <div id="empty" class="d-none">
        <i class="far fa-folder-open"></i>
        {{ trans('laravel-filemanager::lfm.message-empty') }}
      </div>

      <div id="content"></div>

      <a id="item-template" class="d-none">
        <div class="square"></div>

        <div class="info">
          <div class="item_name text-truncate"></div>
          <time class="text-muted font-weight-light text-truncate"></time>
        </div>
      </a>
    </div>

    <div id="fab"></div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="myModalLabel">{{ trans('laravel-filemanager::lfm.title-upload') }}</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aia-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <form action="{{ route('unisharp.lfm.upload') }}" role='form' id='uploadForm' name='uploadForm' method='post' enctype='multipart/form-data' class="dropzone">
            <div class="form-group" id="attachment">
              <div class="controls text-center">
                <div class="input-group w-100">
                  <a class="btn btn-primary w-100 text-white" id="upload-button">{{ trans('laravel-filemanager::lfm.message-choose') }}</a>
                </div>
              </div>
            </div>
            <input type='hidden' name='working_dir' id='working_dir'>
            <input type='hidden' name='type' id='type' value='{{ request("type") }}'>
            <input type='hidden' name='_token' value='{{csrf_token()}}'>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="notify" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-body"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-confirm') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="dialog" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-close') }}</button>
          <button type="button" class="btn btn-primary w-100" data-dismiss="modal">{{ trans('laravel-filemanager::lfm.btn-confirm') }}</button>
        </div>
      </div>
    </div>
  </div>

  <div id="carouselTemplate" class="d-none carousel slide bg-light" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#previewCarousel" data-slide-to="0" class="active"></li>
    </ol>
    <div class="carousel-inner">
      <div class="carousel-item active">
        <a class="carousel-label"></a>
        <div class="carousel-image"></div>
      </div>
    </div>
    <a class="carousel-control-prev" href="#previewCarousel" role="button" data-slide="prev">
      <div class="carousel-control-background" aria-hidden="true">
        <i class="fas fa-chevron-left"></i>
      </div>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#previewCarousel" role="button" data-slide="next">
      <div class="carousel-control-background" aria-hidden="true">
        <i class="fas fa-chevron-right"></i>
      </div>
      <span class="sr-only">Next</span>
    </a>
  </div>

  <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/cropper.min.js') }}"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/dropzone.min.js') }}"></script>
  <script>
    var lang = {!! json_encode(trans('laravel-filemanager::lfm')) !!};
    var actions = [
      // {
      //   name: 'use',
      //   icon: 'check',
      //   label: 'Confirm',
      //   multiple: true
      // },
      {
        name: 'rename',
        icon: 'edit',
        label: lang['menu-rename'],
        multiple: false
      },
      {
        name: 'download',
        icon: 'download',
        label: lang['menu-download'],
        multiple: true
      },
      // {
      //   name: 'preview',
      //   icon: 'image',
      //   label: lang['menu-view'],
      //   multiple: true
      // },
      {
        name: 'move',
        icon: 'paste',
        label: lang['menu-move'],
        multiple: true
      },
      {
        name: 'resize',
        icon: 'arrows-alt',
        label: lang['menu-resize'],
        multiple: false
      },
      {
        name: 'crop',
        icon: 'crop',
        label: lang['menu-crop'],
        multiple: false
      },
      {
        name: 'trash',
        icon: 'trash',
        label: lang['menu-delete'],
        multiple: true
      },
    ];

    var sortings = [
      {
        by: 'alphabetic',
        icon: 'sort-alpha-down',
        label: lang['nav-sort-alphabetic']
      },
      {
        by: 'time',
        icon: 'sort-numeric-down',
        label: lang['nav-sort-time']
      }
    ];
  </script>
  <script>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/script.js')) !!}</script>
  {{-- Use the line below instead of the above if you need to cache the script. --}}
  {{-- <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script> --}}
  <script>
    Dropzone.options.uploadForm = {
      paramName: "upload[]", // The name that will be used to transfer the file
      uploadMultiple: false,
      parallelUploads: 5,
      timeout:0,
      clickable: '#upload-button',
      dictDefaultMessage: lang['message-drop'],
      init: function() {
        var _this = this; // For the closure
        this.on('success', function(file, response) {
          if (response == 'OK') {
            loadFolders();
          } else {
            this.defaultOptions.error(file, response.join('\n'));
          }
        });
      },
      headers: {
        'Authorization': 'Bearer ' + getUrlParam('token')
      },
      acceptedFiles: "{{ implode(',', $helper->availableMimeTypes()) }}",
      maxFilesize: ({{ $helper->maxUploadSize() }} / 1000)
    }
  </script>
</body>
</html>
