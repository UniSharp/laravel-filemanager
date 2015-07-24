<ul class="list-unstyled">
    <li style="margin-left: -5px;">
        <a class="pointer" id="folder_top" data-id="/" onclick="clickRoot()">
            <i class="glyphicon glyphicon-home" data-id="/"></i> Files
        </a>
    </li>
    @foreach($dirs as $key => $dir)
        <li>
            <a class="pointer" id="folder_{{ $key }}" data-id="{{ $dir }}" onclick="clickFolder('folder_{{ $key }}', 0)">
                <i class="fa fa-folder folder-item" data-id="{{ $dir }}" id="{{ $dir }}-folder"></i> {!! $dir !!}
            </a>
        </li>
    @endforeach
</ul>
