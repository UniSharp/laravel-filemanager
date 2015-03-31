<ul class="list-unstyled">
    <li style="margin-left: -5px;">
        <a class="pointer" id="folder_top" data-id="/" onclick="clickFolder('folder_top', 0)">
            <i class="fa fa-folder-open" data-id="/"></i> Files
        </a>
    </li>
    @foreach($dirs as $key => $dir)
        <li>
            <a class="pointer" id="folder_{{ $key }}" data-id="{{ $dir }}" onclick="clickFolder('folder_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir }}"></i> {!! $dir !!}
            </a>
        </li>
    @endforeach
</ul>
