<div class="container">
    <div class="row">

        @if((sizeof($files) > 0) || (sizeof($directories) > 0))

            @foreach($directories as $key => $dir)
                <div class="col-sm-6 col-md-2">
                    <div class="thumbnail text-center" data-id="{{ basename($dir) }}">
                        <a id="folder_{{ $key }}" data-id="{{ $dir }}"
                           onclick="clickFolder('folder_{{ $key }}', 0)"
                           class="folder-icon pointer">
                            {{--<i class="fa fa-folder-o fa-5x"></i>--}}
                            <img src="/vendor/laravel-filemanager/img/folder.jpg">
                        </a>
                    </div>
                    <div class="caption text-center">
                        <div class="btn-group">
                            <button type="button" onclick="clickFolder('folder_{{ $key }}', 0)"
                                    class="btn btn-default btn-xs">
                                {!! str_limit(basename($dir), $limit = 10, $end = '...') !!}
                            </button>
                            <button type="button" class="btn btn-default dropdown-toggle btn-xs" data-toggle="dropdown"
                                    aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="javascript:rename('{!! basename($dir) !!}')">{!! Lang::get('laravel-filemanager::lfm.rename') !!}</a></li>
                                <li><a href="javascript:trash('{!! basename($dir) !!}')">{!! Lang::get('laravel-filemanager::lfm.delete') !!}</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
            @endforeach

            @foreach($files as $key => $file)

                <div class="col-sm-6 col-md-2 img-row">

                    <div class="thumbnail thumbnail-img" data-id="{{ basename($file) }}" id="img_thumbnail_{{ $key }}">
                        <img id="{!! $file !!}"
                             src="{{ $dir_location }}{{ $base }}/thumbs/{{ basename($file) }}?r={{ str_random(40) }}"
                             alt="" style="cursor:pointer;" 
                             onclick="useFile('{!! basename($file) !!}')">
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
                                <!-- <li><a href="javascript:rename('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.rename') !!}</a></li> -->
                                <li><a href="javascript:fileView('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.view') !!}</a></li>
                                <li><a href="javascript:download('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.download') !!}</a></li>
                                <li class="divider"></li>
                                {{--<li><a href="javascript:notImp()">Rotate</a></li>--}}
                                <li><a href="javascript:resizeImage('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.resize') !!}</a></li>
                                <li><a href="javascript:cropImage('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.crop') !!}</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:trash('{!! basename($file) !!}')">{!! Lang::get('laravel-filemanager::lfm.delete') !!}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

            @endforeach

        @else
            <div class="col-md-12">
                <p>{!! Lang::get('laravel-filemanager::lfm.empty_folder') !!}</p>
            </div>
        @endif

    </div>
</div>
