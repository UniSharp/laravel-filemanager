@if(sizeof($items) > 0)

<ul id="list" class="list-unstyled">
  @foreach($items as $item)
  <li>
    @include('laravel-filemanager::image')
  </li>
  @endforeach
</ul>

@else
@include('laravel-filemanager::empty')
@endif
