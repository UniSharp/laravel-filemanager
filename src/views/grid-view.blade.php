@if(sizeof($items) > 0)

<div id="grid" class="row">
  @foreach($items as $item)
  <div class="col-xs-4 col-sm-4 col-md-3 col-lg-2">
    @include('laravel-filemanager::image')
  </div>
  @endforeach
</div>

@else
@include('laravel-filemanager::empty')
@endif
