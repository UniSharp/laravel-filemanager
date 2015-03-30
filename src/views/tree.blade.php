<ul class="list-unstyled">
    @foreach($dirs as $dir)
        <li><a class="folder-item" href="#!" data-id="{{ $dir }}"><i class="fa fa-folder"></i> {!! $dir !!}</a></li>
    @endforeach
</ul>
