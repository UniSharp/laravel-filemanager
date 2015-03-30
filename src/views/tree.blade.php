<ul class="list-unstyled">
    @foreach($dirs as $dir)
        <li>
            <a class="folder-item pointer" data-id="{{ $dir }}">
                <i class="fa fa-folder" data-id="{{ $dir }}"></i> {!! $dir !!}
            </a>
        </li>
    @endforeach
</ul>
