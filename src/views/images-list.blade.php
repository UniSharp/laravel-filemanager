<div class="container">
    @if((sizeof($file_info) > 0) || (sizeof($directories) > 0))
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>{!! Lang::get('laravel-filemanager::lfm.item') !!}</th>
                <th>{!! Lang::get('laravel-filemanager::lfm.size') !!}</th>
                <th>{!! Lang::get('laravel-filemanager::lfm.type') !!}</th>
                <th>{!! Lang::get('laravel-filemanager::lfm.modified') !!}</th>
                <th>{!! Lang::get('laravel-filemanager::lfm.action') !!}</th>
            </tr>
            </thead>
            <tbody>
            @foreach($directories as $key => $dir)
                <tr>
                    <td>
                        <i class="fa fa-folder-o"></i>
                        <a id="large_folder_{{ $key }}" href="javascript:clickFolder('large_folder_{{ $key }}',1)"
                           data-id="{{ $dir }}">
                            {!! basename($dir) !!}
                        </a>
                    </td>
                    <td></td>
                    <td>{!! Lang::get('laravel-filemanager::lfm.folder') !!}</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach

            @foreach($file_info as $file)
                <tr>
                    <td>
                        <i class="fa fa-image"></i>
                        <a href="javascript:useFile('<?= basename($file['name']) ?>')">
                            {!! basename($file['name']) !!}
                        </a>
                        &nbsp;&nbsp;
                        <!-- <a href="javascript:rename('<?= basename($file['name']) ?>')">
                            <i class="fa fa-edit"></i>
                        </a> -->
                    </td>
                    <td>
                        {!! $file['size'] !!}
                    </td>
                    <td>
                        {!! $file['type'] !!}
                    </td>
                    <td>
                        {!! date("Y-m-d h:m", $file['created']) !!}
                    </td>
                    <td>
                        <a href="javascript:trash('<?= basename($file['name']) ?>')">
                            <i class="fa fa-trash fa-fw"></i>
                        </a>
                        <a href="javascript:cropImage('<?= basename($file['name']) ?>')">
                            <i class="fa fa-crop fa-fw"></i>
                        </a>
                        <a href="javascript:resizeImage('<?= basename($file['name']) ?>')">
                            <i class="fa fa-arrows fa-fw"></i>
                        </a>
                        {{--<a href="javascript:notImp()">--}}
                            {{--<i class="fa fa-rotate-left fa-fw"></i>--}}
                        {{--</a>--}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @else
        <div class="col-md-12">
            <p>{!! Lang::get('laravel-filemanager::lfm.empty_folder') !!}</p>
        </div>
    @endif

</div>
