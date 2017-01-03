## Documents

  1. [Installation](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/installation.md)
  1. [Intergration](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/integration.md)
  1. [Config](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/config.md)
  1. [Customization](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/customization.md)
  1. [Events](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/events.md)
  1. [Upgrade](https://github.com/UniSharp/laravel-filemanager/blob/master/docs/upgrade.md)

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

To use events you can add a listener to listen to the events

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

Or by using Event Subscribers

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
