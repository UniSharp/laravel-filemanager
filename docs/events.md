## List of events

* File
  * UniSharp\LaravelFilemanager\Events\FileIsUploading
  * UniSharp\LaravelFilemanager\Events\FileWasUploaded
  * UniSharp\LaravelFilemanager\Events\FileIsRenaming
  * UniSharp\LaravelFilemanager\Events\FileWasRenamed
  * UniSharp\LaravelFilemanager\Events\FileIsMoving
  * UniSharp\LaravelFilemanager\Events\FileWasMoving
  * UniSharp\LaravelFilemanager\Events\FileIsDeleting
  * UniSharp\LaravelFilemanager\Events\FileWasDeleted
* Image
  * UniSharp\LaravelFilemanager\Events\ImageIsUploading
  * UniSharp\LaravelFilemanager\Events\ImageWasUploaded
  * UniSharp\LaravelFilemanager\Events\ImageIsRenaming
  * UniSharp\LaravelFilemanager\Events\ImageWasRenamed
  * UniSharp\LaravelFilemanager\Events\ImageIsResizing
  * UniSharp\LaravelFilemanager\Events\ImageWasResized
  * UniSharp\LaravelFilemanager\Events\ImageIsCropping
  * UniSharp\LaravelFilemanager\Events\ImageWasCropped
  * UniSharp\LaravelFilemanager\Events\ImageIsDeleting
  * UniSharp\LaravelFilemanager\Events\ImageWasDeleted
* Folder
  * UniSharp\LaravelFilemanager\Events\FolderIsCreating
  * UniSharp\LaravelFilemanager\Events\FolderWasCreated
  * UniSharp\LaravelFilemanager\Events\FolderIsRenaming
  * UniSharp\LaravelFilemanager\Events\FolderWasRenamed
  * UniSharp\LaravelFilemanager\Events\FolderIsMoving
  * UniSharp\LaravelFilemanager\Events\FolderWasMoving
  * UniSharp\LaravelFilemanager\Events\FolderIsDeleting
  * UniSharp\LaravelFilemanager\Events\FolderWasDeleted

## How to use
 * Sample code : [laravel-filemanager-demo-events](https://github.com/UniSharp/laravel-filemanager-demo-events)
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
