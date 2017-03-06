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
        <i class="fa {{ $file['icon'] }}"></i>
        <?php $file_name = $file['name'];?>
        <a href="javascript:useFile('{{ $file_name }}')" id="{{ $file_name }}" data-url="{{ $file['url'] }}">
          {{ $file_name }}
        </a>
        &nbsp;&nbsp;
        <a href="javascript:rename('{{ $file_name }}')">
          <i class="fa fa-edit"></i>
        </a>
      </td>
      <td>
        {{ $file['size'] }}
      </td>
      <td>
        {{ $file['type'] }}
      </td>
      <td>
        {{ date("Y-m-d h:m", $file['updated']) }}
      </td>
      <td>
        <a href="javascript:trash('{{ $file_name }}')">
          <i class="fa fa-trash fa-fw"></i>
        </a>
        @if($file['thumb'])
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

<table class="table table-condensed table-striped visible-sm">
  <tbody>
    @foreach($directories as $key => $directory)
    <tr style="height:50px">
      <td>
        <div class="row">
          <div class="col-sm-3">
            <?php $folder_name = $directory->name; ?>
            <?php $folder_path = $directory->path; ?>

            <div class="thumbnail clickable" style="height:45px;width:45px;">
              <div data-id="{{ $folder_path }}" class="folder-item square">
                <img src="{{ asset('vendor/laravel-filemanager/img/folder.png') }}" style="height:40px;width:40px;">
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <a class="folder-item clickable" data-id="{{ $directory->path }}">
              {{ $directory->name }}
            </a>
          </div>
          <div class="col-sm-3"></div>
        </div>
      </td>
    </tr>
    @endforeach

    @foreach($files as $file)
    <tr style="height:50px">
      <td style="width:80vw">
        <div class="row">
          <div class="col-sm-3">
            <?php $file_name = $file['name'];?>
            <?php $thumb_src = $file['thumb'];?>
            <div class="thumbnail clickable" style="height:45px;width:45px;" onclick="useFile('{{ $file_name }}')">
              <div class="square" id="{{ $file_name }}" data-url="{{ $file['url'] }}">
                @if($thumb_src)
                <img src="{{ $thumb_src }}" style="height:40px;width:40px;">
                @else
                <div class="icon-container">
                  <i class="fa {{ $file['icon'] }} fa-5x"></i>
                </div>
                @endif
              </div>
            </div>
          </div>
          <div class="col-sm-6">
            <?php $file_name = $file['name'];?>
            <a href="javascript:useFile('{{ $file_name }}')" id="{{ $file_name }}" data-url="{{ $file['url'] }}">
              {{ $file_name }}
            </a>
            &nbsp;&nbsp;
            <a href="javascript:rename('{{ $file_name }}')">
              <i class="fa fa-edit"></i>
            </a>
            <br>
            {{ date("Y-m-d h:m", $file['updated']) }}
          </div>
          <div class="col-sm-3"></div>
        </div>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

@else
<div class="row">
  <div class="col-md-12">
    <p>{{ Lang::get('laravel-filemanager::lfm.message-empty') }}</p>
  </div>
</div>
@endif
