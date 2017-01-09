<?php $folder_name = $dir_name['short']; ?>
<?php $folder_path = $dir_name['long']; ?>

<div class="thumbnail clickable">
  <div data-id="{{ $folder_path }}" class="folder-item square">
    <img src="{{ asset('vendor/laravel-filemanager/img/folder.png') }}">
  </div>
</div>

<div class="caption text-center">
  <div class="btn-group">
    <button type="button" data-id="{{ $folder_path }}" class="btn btn-default btn-xs folder-item">
      {{ str_limit($folder_name, $limit = 10, $end = '...') }}
    </button>
    <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown" aria-expanded="false">
      <span class="caret"></span>
      <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" role="menu">
      <li><a href="javascript:rename('{{ $folder_name }}')"><i class="fa fa-edit fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-rename') }}</a></li>
      <li><a href="javascript:trash('{{ $folder_name }}')"><i class="fa fa-trash fa-fw"></i> {{ Lang::get('laravel-filemanager::lfm.menu-delete') }}</a></li>
    </ul>
  </div>
</div>
