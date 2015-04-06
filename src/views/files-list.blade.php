<div class="container">
    @if((sizeof($file_info) > 0) || (sizeof($directories) > 0))
        <table class="table table-condensed table-striped">
            <thead>
            <tr>
                <th>Item</th>
                <th>Size</th>
                <th>Type</th>
                <th>Modified</th>
                <th>Action</th>
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
                    <td>Folder</td>
                    <td></td>
                    <td></td>
                </tr>
            @endforeach

            @foreach($file_info as $file)
                <tr>
                    <td>
                        <i class="fa <?= $file['icon']; ?>"></i>
                        <a href="javascript:useFile('<?= basename($file['name']) ?>')">
                            {!! basename($file['name']) !!}
                        </a>
                        &nbsp;&nbsp;
                        <a href="javascript:rename('<?= basename($file['name']) ?>')">
                            <i class="fa fa-edit"></i>
                        </a>
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
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @else
        <div class="col-md-12">
            <p>Folder is empty.</p>
        </div>
    @endif

</div>
