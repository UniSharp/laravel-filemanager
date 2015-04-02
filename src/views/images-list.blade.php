<div class="container">
    @if((sizeof($files) > 0) || (sizeof($directories) > 0))
        <table class="table table-condensed table-striped">
            <thead>
                <tr>
                    <td>Item</td>
                </tr>
            </thead>
            <tbody>
        @foreach($directories as $key => $dir)
            <tr>
                <td>
                    <i class="fa fa-folder-o"></i>
                    <a id="large_folder_{{ $key }}" href="javascript:clickFolder('large_folder_{{ $key }}',1)" data-id="{{ $dir }}">
                        {!! basename($dir) !!}
                    </a>
                </td>
            </tr>
        @endforeach

        @foreach($files as $key => $file)

            <tr>
                <td>
                <i class="fa fa-image"></i>
                <a href="javascript:useFile('{!! basename($file) !!}')">
                    {!! basename($file) !!}
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
