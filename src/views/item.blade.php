<?php $file_name = $file_info[$key]['name'];?>
@if($type == 'Images')
<?php $thumb_src = $thumb_url . $file_name;?>
<?php $thumb_not_found = realpath(public_path($thumb_src)) === false; ?>
@endif

<div class="col-sm-4 col-md-3 col-lg-2 img-row">

  <div class="thumbnail thumbnail-img text-center" data-id="{{ $file_name }}" id="img_thumbnail_{{ $key }}" style="max-width: 210px;max-height: 210px;">
    <div style="width:100%;padding-bottom:100%;position: relative;">
    @if($type == 'Images')
      @if($thumb_not_found)
      <div class="square icon-container">
        <i class="fa fa-image fa-5x pointer" onclick="useFile('{{ $file_name }}')"></i>
      </div>
      @else
      <img id="{{ $file_name }}" src="{{ $thumb_src }}" style="max-width: 100%" class="pointer square" onclick="useFile('{{ $file_name }}')">
      @endif
    @else
    <div class="square icon-container">
      <i class="fa {{ $file['icon'] }} fa-5x pointer" onclick="useFile('{{ $file_name }}')"></i>
    </div>
    @endif
    </div>
  </div>

  <div class="caption text-center">
    <div class="btn-group">
      <button type="button" onclick="useFile('{{ $file_name }}')" class="btn btn-default btn-xs">
        {{ str_limit($file_name, $limit = 10, $end = '...') }}
      </button>
      <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
      </button>
      <ul class="dropdown-menu" role="menu">
        <li><a href="javascript:rename('{{ $file_name }}')"><i class="fa fa-edit fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-rename') }}</a></li>
        <li><a href="javascript:download('{{ $file_name }}')"><i class="fa fa-download fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-download') }}</a></li>
        <li class="divider"></li>
        @if($type == 'Images')
        <li><a href="javascript:fileView('{{ $file_name }}')"><i class="fa fa-image fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-view') }}</a></li>
        {{--<li><a href="javascript:notImp()">Rotate</a></li>--}}
        <li><a href="javascript:resizeImage('{{ $file_name }}')"><i class="fa fa-arrows fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-resize') }}</a></li>
        <li><a href="javascript:cropImage('{{ $file_name }}')"><i class="fa fa-crop fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-crop') }}</a></li>
        <li class="divider"></li>
        @endif
        <li><a href="javascript:trash('{{ $file_name }}')"><i class="fa fa-trash fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</a></li>
      </ul>
    </div>
  </div>
</div>
