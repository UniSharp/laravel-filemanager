<?php
$middlewares = \Config::get('lfm.middlewares');
array_push($middlewares, '\Unisharp\Laravelfilemanager\middleware\MultiUser');

// make sure authenticated
Route::group(array('middleware' => $middlewares, 'prefix' => 'laravel-filemanager'), function ()
{
    // Show LFM
    Route::get('/', 'Unisharp\Laravelfilemanager\controllers\LfmController@show');

    // upload
    Route::any('/upload', 'Unisharp\Laravelfilemanager\controllers\UploadController@upload');

    // list images & files
    Route::get('/jsonitems', 'Unisharp\Laravelfilemanager\controllers\ItemsController@getItems');

    // folders
    Route::get('/newfolder', 'Unisharp\Laravelfilemanager\controllers\FolderController@getAddfolder');
    Route::get('/deletefolder', 'Unisharp\Laravelfilemanager\controllers\FolderController@getDeletefolder');
    Route::get('/folders', 'Unisharp\Laravelfilemanager\controllers\FolderController@getFolders');

    // crop
    Route::get('/crop', 'Unisharp\Laravelfilemanager\controllers\CropController@getCrop');
    Route::get('/cropimage', 'Unisharp\Laravelfilemanager\controllers\CropController@getCropimage');

    // rename
    Route::get('/rename', 'Unisharp\Laravelfilemanager\controllers\RenameController@getRename');

    // scale/resize
    Route::get('/resize', 'Unisharp\Laravelfilemanager\controllers\ResizeController@getResize');
    Route::get('/doresize', 'Unisharp\Laravelfilemanager\controllers\ResizeController@performResize');

    // download
    Route::get('/download', 'Unisharp\Laravelfilemanager\controllers\DownloadController@getDownload');

    // delete
    Route::get('/delete', 'Unisharp\Laravelfilemanager\controllers\DeleteController@getDelete');
});
