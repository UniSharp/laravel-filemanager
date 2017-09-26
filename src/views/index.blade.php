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
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/cropper.min.css') }}">
  <style>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/css/lfm.css')) !!}</style>
  {{-- Use the line below instead of the above if you need to cache the css. --}}
  {{-- <link rel="stylesheet" href="{{ asset('/vendor/laravel-filemanager/css/lfm.css') }}"> --}}
  <link rel="stylesheet" href="{{ asset('vendor/laravel-filemanager/css/mfb.css') }}">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.css">
</head>
<body>
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
      <nav class="navbar navbar-inverse" id="nav">
        <div class="navbar-header">
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
        </div>
        <div class="collapse navbar-collapse" id="nav-buttons">
          <ul class="nav navbar-nav navbar-right">
            <li id="loading" class="hide"><a><i class="fa fa-spinner fa-spin"></i></a></li>
            {{-- <li>
              <a id="multi_selection_toggle">
                <i class="fa fa-check-square fa-fw"></i>
                <span>Multi selection</span>
              </a>
            </li> --}}
            <li>
              <a data-action="use">
                <i class="fa fa-check fa-fw"></i>
                <span>Confirm</span>
              </a>
            </li>
            <li>
              <a data-action="rename">
                <i class="fa fa-edit fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-rename') }}</span>
              </a>
            </li>
            <li>
              <a data-action="download">
                <i class="fa fa-arrow-circle-o-down fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-download') }}</span>
              </a>
            </li>
            <li>
              <a data-action="preview">
                <i class="fa fa-image fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-view') }}</span>
              </a>
            </li>
            <li>
              <a data-action="move">
                <i class="fa fa-share-square-o fa-fw"></i>
                <span>Move</span>
              </a>
            </li>
            <li>
              <a data-action="resize">
                <i class="fa fa-arrows-alt fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-resize') }}</span>
              </a>
            </li>
            <li>
              <a data-action="crop">
                <i class="fa fa-crop fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-crop') }}</span>
              </a>
            </li>
            <li>
              <a data-action="trash">
                <i class="fa fa-trash fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.menu-delete') }}</span>
              </a>
            </li>
            <li>
              <a data-display="grid">
                <i class="fa fa-th-large fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.nav-thumbnails') }}</span>
              </a>
            </li>
            <li>
              <a data-display="list">
                <i class="fa fa-list-ul fa-fw"></i>
                <span>{{ trans('laravel-filemanager::lfm.nav-list') }}</span>
              </a>
            </li>
            <li class="dropdown">
              <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-sort fa-fw"></i>
                {{ trans('laravel-filemanager::lfm.nav-sort') }}
              </a>
              <ul class="dropdown-menu">
                <li>
                  <a data-sortby="alphabetic">
                    <i class="fa fa-sort-alpha-asc"></i> {{ trans('laravel-filemanager::lfm.nav-sort-alphabetic') }}
                  </a>
                </li>
                <li>
                  <a data-sortby="time">
                    <i class="fa fa-sort-amount-asc"></i> {{ trans('laravel-filemanager::lfm.nav-sort-time') }}
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>

      <div class="col-sm-2 hidden-xs" id="tree"></div>

      <div class="col-sm-10 col-xs-12" id="main">
        {{-- <div class="visible-xs" id="current_dir" style="padding: 5px 15px;background-color: #f8f8f8;color: #5e5e5e;"></div> --}}

        <div id="alerts"></div>

        <div id="content"></div>
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

  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/cropper.min.js') }}"></script>
  <script src="{{ asset('vendor/laravel-filemanager/js/jquery.form.min.js') }}"></script>
  <script>
    var route_prefix = "{{ url('/') }}";
    var lfm_route = "{{ url(config('lfm.url_prefix')) }}";
    var lang = {!! json_encode(trans('laravel-filemanager::lfm')) !!};
  </script>
  <script>{!! \File::get(base_path('vendor/unisharp/laravel-filemanager/public/js/script.js')) !!}</script>
  {{-- Use the line below instead of the above if you need to cache the script. --}}
  {{-- <script src="{{ asset('vendor/laravel-filemanager/js/script.js') }}"></script> --}}
  <script>
    $.fn.fab = function (options) {
      var menu = this;
      menu.addClass('mfb-component--br mfb-zoomin').attr('data-mfb-toggle', 'hover');

      var wrapper = $('<li>').addClass('mfb-component__wrap');
      menu.append(wrapper);

      var parent_button = $('<a>');
      parent_button.addClass('mfb-component__button--main')
        .append($('<i>').addClass('mfb-component__main-icon--resting fa fa-plus'))
        .append($('<i>').addClass('mfb-component__main-icon--active fa fa-times'));
      wrapper.append(parent_button);

      var children_list = $('<ul>');
      wrapper.append(children_list);

      options.buttons.forEach(function (button) {
        children_list.append(
          $('<li>').append(
            $('<a>').addClass('mfb-component__button--child')
              .attr('data-mfb-label', button.label)
              .attr('id', button.attrs.id)
              .append(
                $('<i>').addClass('mfb-component__child-icon')
                  .addClass(button.icon)
            )
          )
        );
      });

      children_list.addClass('mfb-component__list');
    };
    $('#fab').fab({
      buttons: [
        {
          icon: 'fa fa-folder',
          label: lang['nav-new'],
          attrs: {id: 'add-folder'}
        },
        {
          icon: 'fa fa-upload',
          label: lang['nav-upload'],
          attrs: {id: 'upload'}
        }
      ]
    });
  </script>
</body>
</html>
