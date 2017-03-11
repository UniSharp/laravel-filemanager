@if((sizeof($files) > 0) || (sizeof($directories) > 0))
<table class="table table-condensed table-striped hidden-sm">
  <thead>
    <th style='width:50%;'>{{ Lang::get('laravel-filemanager::lfm.title-item') }}</th>
    <th>{{ Lang::get('laravel-filemanager::lfm.title-size') }}</th>
    <th>{{ Lang::get('laravel-filemanager::lfm.title-type') }}</th>
    <th>{{ Lang::get('laravel-filemanager::lfm.title-modified') }}</th>
    <th>{{ Lang::get('laravel-filemanager::lfm.title-action') }}</th>
  </thead>
  <tbody>
    @foreach($directories as $key => $directory)
    <tr>
      <td>
        <i class="fa fa-folder-o"></i>
        <a class="folder-item clickable" data-id="{{ $directory->path }}">
          {{ $directory->name }}
        </a>
      </td>
      <td></td>
      <td>{{ Lang::get('laravel-filemanager::lfm.type-folder') }}</td>
      <td></td>
      <td></td>
    </tr>
    @endforeach

    @foreach($files as $file)
    <tr>
      <td>
        <i class="fa {{ $file->icon }}"></i>
        <?php $file_name = $file->name;?>
        <a href="javascript:useFile('{{ $file_name }}')" id="{{ $file_name }}" data-url="{{ $file->url }}">
          {{ $file_name }}
        </a>
        &nbsp;&nbsp;
        <a href="javascript:rename('{{ $file_name }}')">
          <i class="fa fa-edit"></i>
        </a>
      </td>
      <td>
        {{ $file->size }}
      </td>
      <td>
        {{ $file->type }}
      </td>
      <td>
        {{ date("Y-m-d h:m", $file->updated) }}
      </td>
      <td>
        <a href="javascript:trash('{{ $file_name }}')">
          <i class="fa fa-trash fa-fw"></i>
        </a>
        @if($file->thumb)
        <a href="javascript:cropImage('{{ $file_name }}')">
          <i class="fa fa-crop fa-fw"></i>
        </a>
        <a href="javascript:resizeImage('{{ $file_name }}')">
          <i class="fa fa-arrows fa-fw"></i>
        </a>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<table class="table visible-sm">
  <tbody>
    @foreach($items as $item)
    <tr>
      <td>
        <div class="media">
          <div class="media-left">
            <div class="clickable thumbnail-mobile">
              @if(!$item->is_file)
              <div class="square folder-item" data-id="{{ $item->path }}">
              @else
              <div class="square" id="{{ $item->name }}" data-url="{{ $item->url }}">
              @endif
                @if($item->thumb)
                <img src="{{ $item->thumb }}">
                @else
                <div class="icon-container">
                  <i class="fa {{ $item->icon }} fa-5x"></i>
                </div>
                @endif
              </div>
            </div>
          </div>
          <div class="media-body" style="padding-top: 40px;padding-bottom: 40px">
            <div class="media-heading">
              <p style="font-size:70px">
                @if(!$item->is_file)
                <a class="folder-item clickable" data-id="{{ $item->path }}">
                @else
                <a href="javascript:useFile('{{ $item->name }}')" id="{{ $item->name }}" data-url="{{ $item->url }}">
                @endif
                  {{ str_limit($item->name, $limit = 10, $end = '...') }}
                </a>
                &nbsp;&nbsp;
                {{-- <a href="javascript:rename('{{ $item->name }}')">
                  <i class="fa fa-edit"></i>
                </a> --}}
              </p>
            </div>
            <p style="font-size:50px;color: #aaa;font-weight: 400">{{ $item->time }}</p>
          </div>
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

@else
<p>{{ trans('laravel-filemanager::lfm.message-empty') }}</p>
@endif
