@if(sizeof($items) > 0)
<table class="table" id="list">
  <tbody>
    @foreach($items as $item)
    <tr>
      <td>
        <div class="media" style="height: 70px;">
          <div class="media-left">
            <div class="square {{ $item->is_file ? 'file' : 'folder'}}-item clickable"  data-id="{{ $item->path }}">
              @if($item->thumb)
              <img src="{{ $item->thumb }}">
              @else
              <i class="fa {{ $item->icon }} fa-5x"></i>
              @endif
            </div>
          </div>
          <div class="media-body" style="padding-top: 10px;">
            <div class="media-heading">
              <p>
                <a class="{{ $item->is_file ? 'file' : 'folder'}}-item clickable" data-id="{{ $item->path }}">
                  {{ str_limit($item->name, $limit = 20, $end = '...') }}
                </a>
                &nbsp;&nbsp;
                <a href="javascript:rename('{{ $item->name }}')">
                  <i class="fa fa-edit"></i>
                </a>
                @if($item->is_file)
                <a href="javascript:trash('{{ $item->name }}')">
                  <i class="fa fa-trash fa-fw"></i>
                </a>
                @if($item->thumb)
                <a href="javascript:cropImage('{{ $item->name }}')">
                  <i class="fa fa-crop fa-fw"></i>
                </a>
                <a href="javascript:resizeImage('{{ $item->name }}')">
                  <i class="fa fa-arrows fa-fw"></i>
                </a>
                @endif
                @endif
              </p>
            </div>
            <p style="color: #aaa;font-weight: 400">{{ date('Y-m-d h:m', $item->time) }}</p>
          </div>
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

@else
@include('laravel-filemanager::empty')
@endif
