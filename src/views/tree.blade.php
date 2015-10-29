<ul class="list-unstyled">
    @if(Config::get('lfm.allow_multi_user'))
    <li style="margin-left: -10px;">
        <a class="pointer" id="folder_root" data-id="/" onclick="clickRoot()">
            <i class="fa fa-folder-open" data-id="/"></i> {{ Lang::get('laravel-filemanager::lfm.title-root') }}
        </a>
    </li>
    @foreach($dirs as $key => $dir)
        <li>
            <a class="pointer" id="folder_{{ $key }}" data-id="{{ $dir }}" onclick="clickFolder('folder_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir }}" id="{{ $dir }}-folder"></i> {{ $dir }}
            </a>
        </li>
    @endforeach
    <hr>
    @endif
    <li style="margin-left: -10px;">
        <a class="pointer" id="folder_shared" data-id="/" onclick="clickShared()">
            <i class="fa fa-folder" data-id="/"></i> {{ Lang::get('laravel-filemanager::lfm.title-shares') }}
        </a>
    </li>
    @foreach($shares as $key => $dir)
        <li>
            <a class="pointer" id="shared_{{ $key }}" data-id="{{ $dir }}" onclick="clickSharedFolder('shared_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir }}" id="{{ $dir }}-folder-shared"></i> {{ $dir }}
            </a>
        </li>
    @endforeach
</ul>
