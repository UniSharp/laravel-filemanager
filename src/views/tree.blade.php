<ul class="list-unstyled">
    @foreach($dirs as $key => $dir)
        <li>
            <a class="pointer" id="folder_{{ $key }}" data-id="{{ $dir }}" onclick="clickFolder('folder_{{ $key }}')">
                <i class="fa fa-folder folder-item" data-id="{{ $dir }}"></i> {!! $dir !!}
            </a>
        </li>
    @endforeach
</ul>
