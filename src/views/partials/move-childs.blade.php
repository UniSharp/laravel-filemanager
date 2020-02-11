@if($childs)
  <ul class="nav-pills">
    @foreach($childs as $child)
      <li class="nav-item sub-item">
        <a class="nav-link" href="#" data-type="0" onclick="moveToNewFolder(`{{ $child->url }}`)">
          <i class="fa fa-folder fa-fw"></i> {{ $child->name }}
          <input type="hidden" id="goToFolder" name="goToFolder" value="{{ $child->url }}">
        </a>
      </li>
      @include('vendor.laravel-filemanager.partials.move-childs', ['childs' => $lfm->dir($child->url)->folders()])
    @endforeach
  </ul>
@endif
