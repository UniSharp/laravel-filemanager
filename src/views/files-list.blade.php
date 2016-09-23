<div class="container">

  @if((sizeof($file_info) > 0) || (sizeof($directories) > 0))
  <table class="table table-condensed table-striped">
    <thead>
      <th style='width:50%;'>{{ Lang::get('laravel-filemanager::lfm.title-item') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-size') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-type') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-modified') }}</th>
      <th>
        @if($options['remove'])
        {{ Lang::get('laravel-filemanager::lfm.title-action') }}</th>
        @endif
    </thead>
    <tbody>
      @foreach($directories as $key => $dir_name)
      <tr>
        <td>
          <i class="fa fa-folder-o"></i>
          <a class="folder-item pointer" data-id="{{ $dir_name['long'] }}" title="{{ $dir_name['base'] }}">
            {{ $dir_name['base'] }}
          </a>
          @if($options['rename'])
            <a href="javascript:rename('{{ $dir_name['base'] }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-rename') }}"><i class="fa fa-edit fa-fw"></i></a>
          @endif
        </td>
        <td></td>
        <td>{{ Lang::get('laravel-filemanager::lfm.type-folder') }}</td>
        <td></td>
        <td>
          @if($options['remove'])
          <a href="javascript:trash('{{ $dir_name['base'] }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-delete') }}"><i class="fa fa-trash fa-fw"></i></a>
          @endif
        </td>
      </tr>
      @endforeach

      @foreach($file_info as $file)
      <tr>
        <td>
          <i class="fa {{ $file['icon'] }}"></i>
          <?php $file_name = $file['name'];?>
          <a href="javascript:useFile('{{ $file_name }}')" title="{{ $file_name }}">
            {{ $file_name }}
          </a>
          @if($options['rename'])
          &nbsp;&nbsp;
          <a href="javascript:rename('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-rename') }}">
            <i class="fa fa-edit"></i>
          </a>
          @endif
        </td>
        <td>
          {{ $file['size'] }}
        </td>
        <td>
          {{ $file['type'] }}
        </td>
        <td>
          {{ date("Y-m-d h:m", $file['created']) }}
        </td>
        <td>
          @if($options['remove'])
          <a href="javascript:trash('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-delete') }}">
            <i class="fa fa-trash fa-fw"></i>
          </a>
          @endif
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

</div>
