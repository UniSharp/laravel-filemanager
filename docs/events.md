## List of events
 * Xuandung38\LaravelFilemanager\Events\ImageIsUploading
 * Xuandung38\LaravelFilemanager\Events\ImageWasUploaded
 * Xuandung38\LaravelFilemanager\Events\ImageIsRenaming
 * Xuandung38\LaravelFilemanager\Events\ImageWasRenamed
 * Xuandung38\LaravelFilemanager\Events\ImageIsDeleting
 * Xuandung38\LaravelFilemanager\Events\ImageWasDeleted
 * Xuandung38\LaravelFilemanager\Events\FolderIsRenaming
 * Xuandung38\LaravelFilemanager\Events\FolderWasRenamed
 * Xuandung38\LaravelFilemanager\Events\ImageIsResizing
 * Xuandung38\LaravelFilemanager\Events\ImageWasResized
 * Xuandung38\LaravelFilemanager\Events\ImageIsCropping
 * Xuandung38\LaravelFilemanager\Events\ImageWasCropped


## How to use
 * Sample code : [laravel-filemanager-demo-events](https://github.com/Xuandung38/laravel-filemanager-demo-events)
 * To use events you can add a listener to listen to the events.

    Snippet for `EventServiceProvider`

    ```php
    protected $listen = [
        ImageWasUploaded::class => [
            UploadListener::class,
        ],
    ];
    ```

    The `UploadListener` will look like:

    ```php
    class UploadListener
    {
        public function handle($event)
        {
            $method = 'on'.class_basename($event);
            if (method_exists($this, $method)) {
                call_user_func([$this, $method], $event);
            }
        }

        public function onImageWasUploaded(ImageWasUploaded $event)
        {
            $path = $event->path();
            //your code, for example resizing and cropping
        }
    }
    ```

 * Or by using Event Subscribers

    Snippet for `EventServiceProvider`

    ```php
    protected $subscribe = [
        UploadListener::class
    ];
    ```

    The `UploadListener` will look like:

    ```php
    public function subscribe($events)
    {
        $events->listen('*', UploadListener::class);
    }

    public function handle($event)
    {
        $method = 'on'.class_basename($event);
        if (method_exists($this, $method)) {
            call_user_func([$this, $method], $event);
        }
    }

    public function onImageWasUploaded(ImageWasUploaded $event)
    {
        $path = $event->path();
        // your code, for example resizing and cropping
    }

    public function onImageWasRenamed(ImageWasRenamed $event)
    {
        // image was renamed
    }

    public function onImageWasDeleted(ImageWasDeleted $event)
    {
        // image was deleted
    }

    public function onFolderWasRenamed(FolderWasRenamed $event)
    {
        // folder was renamed
    }
    ```
