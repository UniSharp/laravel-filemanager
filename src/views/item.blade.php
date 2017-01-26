<?php $file_name = $file['name'];?>
<?php $thumb_src = $file['thumb'];?>

<div class="thumbnail clickable" onclick="useFile('{{ $file_name }}')">
  <div class="square" id="{{ $file_name }}" data-url="{{ $file['url'] }}">
    @if($thumb_src)
    <img src="{{ $thumb_src }}">
    @else
    <div class="icon-container">
      <i class="fa {{ $file['icon'] }} fa-5x"></i>
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
      @if($thumb_src)
      <li><a href="javascript:fileView('{{ $file_name }}', '{{ $file["updated"] }}')"><i class="fa fa-image fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-view') }}</a></li>
      <li><a href="javascript:resizeImage('{{ $file_name }}')"><i class="fa fa-arrows fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-resize') }}</a></li>
      <li><a href="javascript:cropImage('{{ $file_name }}')"><i class="fa fa-crop fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-crop') }}</a></li>
      <li class="divider"></li>
      @endif
      <li><a href="javascript:trash('{{ $file_name }}')"><i class="fa fa-trash fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</a></li>
    </ul>
  </div>
</div>
