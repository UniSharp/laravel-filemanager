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
{{--                        <p>{{ basename($dir) }}</p>--}}

                    </div>
                </div>
            @endforeach

            @foreach($files as $key => $file)

                <div class="col-sm-6 col-md-2 img-row" style="">

                    <div class="thumbnail thumbnail-img" data-id="{{ basename($file) }}" id="img_thumbnail_{{ $key }}">
                        <img id="{!! $file !!}"
                             src="/vendor/laravel-filemanager/files/{{ $base }}/thumbs/{{ basename($file) }}?r={{ str_random(40) }}"
                             alt="">
                    </div>
                    <div class="caption text-center">

                        <div class="btn-group ">
                            <button type="button" onclick="useFile('{!! basename($file) !!}')" class="btn btn-default btn-xs">
                                {!! str_limit(basename($file), $limit = 10, $end = '...') !!}
                            </button>
                            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:rename('{!! basename($file) !!}')">Rename</a></li>
                                <li><a href="javascript:cropImage('{!! basename($file) !!}')">View</a></li>
                                <li><a href="javascript:cropImage('{!! basename($file) !!}')">Download</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:cropImage('{!! basename($file) !!}')">Rotate</a></li>
                                <li><a href="javascript:scaleImage('{!! basename($file) !!}')">Scale</a></li>
                                <li><a href="javascript:cropImage('{!! basename($file) !!}')">Crop</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:trash('{!! basename($file) !!}')">Delete</a></li>
                            </ul>
                            <p>&nbsp;</p>
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
