@foreach($files as $file)
    <div class="col-sm-6 col-md-2">
        <a href="#" class="thumbnail" data-id="{{ basename($file) }}">
            <img src="/vendor/laravel-filemanager/files/{{ $base }}/thumbs/{{ basename($file) }}">
        </a>
    </div>
@endforeach