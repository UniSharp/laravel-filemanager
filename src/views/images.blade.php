<div class="container">
  <div class="row">

    @if((sizeof($files) > 0) || (sizeof($directories) > 0))

    @foreach($directories as $key => $dir_name)
    <?php $long_name = $dir_name['long'];?>
    <div class="col-sm-6 col-md-2">
      <div class="thumbnail text-center" data-id="{{ $dir_name['long'] }}">
        <a data-id="{{ $dir_name['long'] }}" onclick="clickFolder('{{ $long_name }}')" class="folder-icon pointer">
          {{--<i class="fa fa-folder-o fa-5x"></i>--}}
          <img src="/vendor/laravel-filemanager/img/folder.jpg">
        </a>
      </div>
      <div class="caption text-center">
        <div class="btn-group">
          <button type="button" onclick="clickFolder('{{ $long_name }}')" class="btn btn-default btn-xs">
            {{ str_limit($dir_name['short'], $limit = 10, $end = '...') }}
          </button>
          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:rename('{{ $dir_name['short'] }}')">{{ Lang::get('laravel-filemanager::lfm.menu-rename') }}</a></li>
            <li><a href="javascript:trash('{{ $dir_name['short'] }}')">{{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</a></li>
          </ul>
        </div>

      </div>
    </div>
    @endforeach

    @foreach($files as $key => $file)

    <?php $file_name = $file_info[$key]['name'];?>
    <?php $thumb_src = $thumb_url . $file_name;?>

    <div class="col-sm-6 col-md-2 img-row">

      <div class="thumbnail thumbnail-img" data-id="{{ $file_name }}" id="img_thumbnail_{{ $key }}">
        <img id="{{ $file }}" src="{{ $thumb_src }}" alt="" class="pointer" onclick="useFile('{{ $file_name }}')">
      </div>

      <div class="caption text-center">
        <div class="btn-group ">
          <button type="button" onclick="useFile('{{ $file_name }}')" class="btn btn-default btn-xs">
            {{ str_limit($file_name, $limit = 10, $end = '...') }}
          </button>
          <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
            <span class="caret"></span>
            <span class="sr-only">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu" role="menu">
            <li><a href="javascript:rename('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-rename') }}</a></li>
            <li><a href="javascript:fileView('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-view') }}</a></li>
            <li><a href="javascript:download('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-download') }}</a></li>
            <li class="divider"></li>
            {{--<li><a href="javascript:notImp()">Rotate</a></li>--}}
            <li><a href="javascript:resizeImage('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-resize') }}</a></li>
            <li><a href="javascript:cropImage('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-crop') }}</a></li>
            <li class="divider"></li>
            <li><a href="javascript:trash('{{ $file_name }}')">{{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</a></li>
          </ul>
        </div>
      </div>
    </div>

    @endforeach

    @else
    <div class="col-md-12">
      <p>{{ Lang::get('laravel-filemanager::lfm.message-empty') }}</p>
    </div>
    @endif

  </div>
</div>
