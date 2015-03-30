@if(sizeof($files) > 0)
    @foreach($files as $file)

        <div class="col-sm-6 col-md-2">
            <div class="thumbnail">
                <img id="{!! $file !!}"
                     src="/vendor/laravel-filemanager/files/{{ $base }}/thumbs/{{ basename($file) }}"
                     alt="">
            </div>
            <div class="caption text-center">
                <h5>{{ basename($file) }}</h5>

                <p class="text-center">
                    <a href="#" class="btn btn-primary btn-xs" role="button">
                        Use this image
                    </a>
                </p>
            </div>
        </div>

    @endforeach

@else
    <p>Folder is empty.</p>
@endif
