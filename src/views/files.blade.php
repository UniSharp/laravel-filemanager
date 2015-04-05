<div class="container">
    <div class="row">

        @if((sizeof($files) > 0) || (sizeof($directories) > 0))

            @foreach($directories as $key => $dir)
                <div class="col-sm-6 col-md-2">
                    <div class="thumbnail text-center" data-id="{{ basename($dir) }}">
                        <a id="large_folder_{{ $key }}" data-id="{{ $dir }}"
                           onclick="clickFolder('large_folder_{{ $key }}',1)"
                           class="folder-icon pointer">
                            <img src="/vendor/laravel-filemanager/img/folder.jpg">
                        </a>
                    </div>
                    <div class="caption text-center">
                        <div class="btn-group">
                            <button type="button" onclick="clickFolder('large_folder_{{ $key }}',1)"
                                    class="btn btn-default btn-xs">
                                {!! str_limit(basename($dir), $limit = 10, $end = '...') !!}
                            </button>
                            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:rename('{!! basename($dir) !!}')">Rename</a></li>
                                <li><a href="javascript:trash('{!! basename($dir) !!}')">Delete</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            @endforeach

            @foreach($file_info as $key => $file)

                <div class="col-sm-6 col-md-2 img-row">

                    <div class="thumbnail thumbnail-img text-center" data-id="{{ basename($file['name']) }}" id="img_thumbnail_{{ $key }}">
                        <i class="fa <?= $file['icon'] ?> fa-5x"></i>
                    </div>

                    <div class="caption text-center">
                        <div class="btn-group ">
                            <button type="button" onclick="useFile('<?= basename($file['name']) ?>')" class="btn btn-default btn-xs">
                                {!! str_limit(basename($file['name']), $limit = 10, $end = '...') !!}
                            </button>
                            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:rename('<?= basename($file['name']) ?>')">Rename</a></li>
                                <li><a href="javascript:fileView('<?= basename($file['name']) ?>')">View</a></li>
                                <li><a href="javascript:download('<?= basename($file['name']) ?>')">Download</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:notImp()">Rotate</a></li>
                                <li><a href="javascript:resizeImage('<?= basename($file['name']) ?>')">Resize</a></li>
                                <li><a href="javascript:cropImage('<?= basename($file['name']) ?>')">Crop</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:trash('<?= basename($file['name']) ?>')">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            @endforeach

        @else
            <div class="col-md-12">
                <p>Folder is empty.</p>
            </div>
        @endif

    </div>
</div>
