<?php $item_name = $item->name; ?>
<?php $thumb_src = $item->thumb; ?>
<?php $item_path = $item->path; ?>
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
    <span class="caret"></span>
    <span class="sr-only">Toggle Dropdown</span>
  </button>
  <ul class="dropdown-menu dropdown-menu-right" role="menu">
    <li><span onclick="javascript:rename('{{ $item_name }}')"><i class="fa fa-edit fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-rename') }}</span></li>
    @if($item->is_file)
      <li><span onclick="javascript:download('{{ $item_name }}')"><i class="fa fa-download fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-download') }}</span></li>
      <li class="divider"></li>
      @if($thumb_src)
        <li><span onclick="javascript:fileView('{{ $item_path . '?timestamp=' . $item->time }}')"><i class="fa fa-image fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-view') }}</span></li>
        <li><span onclick="javascript:resizeImage('{{ $item_name }}')"><i class="fa fa-arrows fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-resize') }}</span></li>
        <li><span onclick="javascript:cropImage('{{ $item_name }}')"><i class="fa fa-crop fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-crop') }}</span></li>
        <li class="divider"></li>
      @endif
    @endif
    <li><span onclick="javascript:trash('{{ $item_name }}')"><i class="fa fa-trash fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</span></li>
  </ul>
</div>
