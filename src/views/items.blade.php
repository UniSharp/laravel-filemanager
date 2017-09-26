@if(sizeof($items) > 0)

<ul id="{{$display}}" class="{{$display === 'grid' ? 'row' : ''}} list-unstyled">
  @foreach($items as $item)
  <li class="{{$display === 'grid' ? 'col-xs-4 col-sm-4 col-md-3 col-lg-2' : ''}}">
    <a data-path="{{ $item->path }}"
      data-name="{{ $item->name }}"
      data-time="{{ $item->time }}"
      data-type="{{ (int)$item->is_file }}"
      data-image="{{ (int)$item->is_image }}">
      <div class="square">
        @if($item->thumb_url)
        <div class="img-bordered" style="background-image: url('{{ $item->thumb_url }}');"></div>
        @else
        <i class="fa {{ $item->icon }} fa-5x"></i>
        @endif
      </div>

      <div>
        <div class="item_name">{{ $item->name }}</div>
        <time>{{ date('Y-m-d h:m', $item->time) }}</time>
      </div>
    </a>
  </li>
  @endforeach
</ul>

@else

<div class="alert alert-warning">
  <i class="fa fa-folder-open-o"></i> {{ trans('laravel-filemanager::lfm.message-empty') }}
</div>

@endif
