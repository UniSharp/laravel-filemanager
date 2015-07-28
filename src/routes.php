<?php

Route::group(array('middleware' => ['auth', 'myfolder'], 'prefix' => 'laravel-filemanager'), function () {
    // Show LFM
    Route::get('/', '\Tsawler\Laravelfilemanager\controllers\LfmController@show');

    // upload
    Route::any('/upload', '\Tsawler\Laravelfilemanager\controllers\UploadController@upload');

    // list images & files
    Route::get('/jsonimages', '\Tsawler\Laravelfilemanager\controllers\ItemsController@getImages');
    Route::get('/jsonfiles', '\Tsawler\Laravelfilemanager\controllers\ItemsController@getFiles');

    // folders
    Route::get('/newfolder', '\Tsawler\Laravelfilemanager\controllers\FolderController@getAddfolder');
    Route::get('/deletefolder', '\Tsawler\Laravelfilemanager\controllers\FolderController@getDeletefolder');
    Route::get('/folders', '\Tsawler\Laravelfilemanager\controllers\FolderController@getFolders');

    // crop
    Route::get('/crop', '\Tsawler\Laravelfilemanager\controllers\CropController@getCrop');
    Route::get('/cropimage', '\Tsawler\Laravelfilemanager\controllers\CropController@getCropimage');

    // rename
    Route::get('/rename', '\Tsawler\Laravelfilemanager\controllers\RenameController@getRename');

    // scale/resize
    Route::get('/resize', '\Tsawler\Laravelfilemanager\controllers\ResizeController@getResize');
    Route::get('/doresize', '\Tsawler\Laravelfilemanager\controllers\ResizeController@performResize');

    // download
    Route::get('/download', '\Tsawler\Laravelfilemanager\controllers\DownloadController@getDownload');

    // delete
    Route::get('/delete', '\Tsawler\Laravelfilemanager\controllers\DeleteController@getDelete');
});
