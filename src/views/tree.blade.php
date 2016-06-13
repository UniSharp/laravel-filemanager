<ul class="list-unstyled">
  @if(Config::get('lfm.allow_multi_user'))
  <li style="margin-left: -10px;">
    <a class="pointer folder-item" data-id="{{ $user_dir }}">
      <i class="fa fa-folder-open"></i> {{ Lang::get('laravel-filemanager::lfm.title-root') }}
    </a>
  </li>
  @foreach($dirs as $key => $dir_name)
  <li>
    <a class="pointer folder-item" data-id="{{ $dir_name['long'] }}">
      <i class="fa fa-folder"></i> {{ $dir_name['short'] }}
    </a>
  </li>
  @endforeach
  <hr>
  @endif
  <li style="margin-left: -10px;">
    <a class="pointer folder-item" data-id="{{ $share_dir }}">
      <i class="fa fa-folder"></i> {{ Lang::get('laravel-filemanager::lfm.title-shares') }}
    </a>
  </li>
  @foreach($shares as $key => $dir_name)
  <li>
    <a class="pointer folder-item" data-id="{{ $dir_name['long'] }}">
      <i class="fa fa-folder"></i> {{ $dir_name['short'] }}
    </a>
  </li>
  @endforeach
</ul>
