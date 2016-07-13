<?php $file_name = $file_info[$key]['name'];?>
@if($type == 'Images')
<?php $thumb_src = $thumb_url . $file_name;?>
@endif

<div class="col-sm-4 col-md-3 col-lg-2 img-row">

  <div class="thumbnail thumbnail-img text-center" data-id="{{ $file_name }}" id="img_thumbnail_{{ $key }}">
    @if($type == 'Images')
    <img id="{{ $file_name }}" src="{{ asset($thumb_src) }}" alt="" class="pointer" onclick="useFile('{{ $file_name }}')">
    @else
    <i class="fa {{ $file['icon'] }} fa-5x" style="height:200px;cursor:pointer;padding-top:60px;" onclick="useFile('{{ $file_name }}')"></i>
    @endif
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
