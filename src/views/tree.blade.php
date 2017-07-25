<ul class="list-unstyled">
  @foreach($root_folders as $root_folder)
    <li>
      <a data-type="0" data-path="{{ $root_folder->path }}">
        <i class="fa fa-folder"></i> {{ $root_folder->name }}
      </a>
    </li>
    @foreach($root_folder->children as $directory)
      <li style="margin-left: 10px;">
        <a data-type="0" data-path="{{ $directory->path }}">
          <i class="fa fa-folder"></i> {{ $directory->name }}
        </a>
      </li>
    @endforeach
    @if($root_folder->has_next)
      <hr>
    @endif
  @endforeach
</ul>
