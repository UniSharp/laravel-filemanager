<ul class="list-unstyled">
    @if(Config::get('lfm.allow_multi_user'))
    <li style="margin-left: -10px;">
        <a class="pointer" id="folder_root" data-id="/" onclick="clickRoot()">
            <i class="fa fa-folder-open" data-id="/"></i> {{ Lang::get('laravel-filemanager::lfm.title-root') }}
        </a>
    </li>
    @foreach($dirs as $key => $dir_name)
        <li>
            <a class="pointer" id="folder_{{ $key }}" data-id="{{ $dir_name }}" onclick="clickFolder('folder_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir_name }}" id="{{ $dir_name }}-folder"></i> {{ $dir_name }}
            </a>
        </li>
    @endforeach
    <a id="add-folder" class="add-folder btn btn-default btn-xs" style='margin-top:15px;'>
        <i class="fa fa-plus"></i> {{ Lang::get('laravel-filemanager::lfm.menu-new') }}
    </a>
    <hr>
    @endif
    <li style="margin-left: -10px;">
        <a class="pointer" id="folder_shared" data-id="/" onclick="clickShared()">
            <i class="fa fa-folder" data-id="/"></i> {{ Lang::get('laravel-filemanager::lfm.title-shares') }}
        </a>
    </li>
    @foreach($shares as $key => $dir_name)
        <li>
            <a class="pointer" id="shared_{{ $key }}" data-id="{{ $dir_name }}" onclick="clickSharedFolder('shared_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir_name }}" id="{{ $dir_name }}-folder-shared"></i> {{ $dir_name }}
            </a>
        </li>
    @endforeach
</ul>
