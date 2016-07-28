<?php
$middlewares = \Config::get('lfm.middlewares');
array_push($middlewares, '\Jayked\Laravelfilemanager\middleware\MultiUser');

// make sure authenticated
Route::group(array('middleware' => $middlewares, 'prefix' => 'laravel-filemanager'), function ()
{
    // Show LFM
    Route::get('/', 'Jayked\Laravelfilemanager\controllers\LfmController@show');

    // upload
    Route::any('/upload', 'Jayked\Laravelfilemanager\controllers\UploadController@upload');

    // list images & files
    Route::get('/jsonitems', 'Jayked\Laravelfilemanager\controllers\ItemsController@getItems');

    // folders
    Route::get('/newfolder', 'Jayked\Laravelfilemanager\controllers\FolderController@getAddfolder');
    Route::get('/deletefolder', 'Jayked\Laravelfilemanager\controllers\FolderController@getDeletefolder');
    Route::get('/folders', 'Jayked\Laravelfilemanager\controllers\FolderController@getFolders');

    // crop
    Route::get('/crop', 'Jayked\Laravelfilemanager\controllers\CropController@getCrop');
    Route::get('/cropimage', 'Jayked\Laravelfilemanager\controllers\CropController@getCropimage');

    // rename
    Route::get('/rename', 'Jayked\Laravelfilemanager\controllers\RenameController@getRename');

    // scale/resize
    Route::get('/resize', 'Jayked\Laravelfilemanager\controllers\ResizeController@getResize');
    Route::get('/doresize', 'Jayked\Laravelfilemanager\controllers\ResizeController@performResize');

    // download
    Route::get('/download', 'Jayked\Laravelfilemanager\controllers\DownloadController@getDownload');

    // delete
    Route::get('/delete', 'Jayked\Laravelfilemanager\controllers\DeleteController@getDelete');
});
