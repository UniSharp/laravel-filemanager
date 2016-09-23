<div class="container">

  @if((sizeof($file_info) > 0) || (sizeof($directories) > 0))
    <table class="table table-condensed table-striped">
    <thead>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-item') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-size') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-type') }}</th>
      <th>{{ Lang::get('laravel-filemanager::lfm.title-modified') }}</th>
      <th>
        @if($options['remove'] || $options['crop'] || $options['resize'])
          {{ Lang::get('laravel-filemanager::lfm.title-action') }}
        @endif
      </th>
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
          <i class="fa fa-image"></i>
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
          <a href="javascript:fileView('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-view') }}"><i class="fa fa-image fa-fw"></i></a>
          <a href="javascript:download('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-download') }}"><i class="fa fa-download fa-fw"></i></a>
          @if($options['remove'])
            <a href="javascript:trash('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-delete') }}">
            <i class="fa fa-trash fa-fw"></i>
          </a>
          @endif
          @if($options['crop'])
            <a href="javascript:cropImage('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-crop') }}">
            <i class="fa fa-crop fa-fw"></i>
          </a>
          @endif
          @if($options['resize'])
            <a href="javascript:resizeImage('{{ $file_name }}')" title="{{ Lang::get('laravel-filemanager::lfm.menu-resize') }}">
            <i class="fa fa-arrows fa-fw"></i>
          </a>
          @endif
          {{--<a href="javascript:notImp()">--}}
          {{--<i class="fa fa-rotate-left fa-fw"></i>--}}
          {{--</a>--}}
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
