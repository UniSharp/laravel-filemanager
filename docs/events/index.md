## Documents
  1. [Installation](https://unisharp.github.io/laravel-filemanager/installation)
  1. [Integration](https://unisharp.github.io/laravel-filemanager/integration)
  1. [Config](https://unisharp.github.io/laravel-filemanager/config)
  1. [Customization](https://unisharp.github.io/laravel-filemanager/customization)
  1. [Events](https://unisharp.github.io/laravel-filemanager/events)
  1. [Upgrade](https://unisharp.github.io/laravel-filemanager/upgrade)

## List of events
 * Unisharp\Laravelfilemanager\Events\ImageIsUpload
 * Unisharp\Laravelfilemanager\Events\ImageWasUploaded
 * Unisharp\Laravelfilemanager\Events\ImageIsRenaming
 * Unisharp\Laravelfilemanager\Events\ImageWasRenamed
 * Unisharp\Laravelfilemanager\Events\ImageIsDeleting
 * Unisharp\Laravelfilemanager\Events\ImageWasDeleted
 * Unisharp\Laravelfilemanager\Events\FolderIsRenaming
 * Unisharp\Laravelfilemanager\Events\FolderWasRenamed

## How to use
 * To use events you can add a listener to listen to the events

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
