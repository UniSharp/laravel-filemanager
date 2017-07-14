<?php

$middleware = array_merge(\Config::get('lfm.middlewares'), [
    '\UniSharp\LaravelFilemanager\middlewares\CreateDefaultFolder',
    '\UniSharp\LaravelFilemanager\middlewares\MultiUser',
]);
$prefix = \Config::get('lfm.prefix', 'laravel-filemanager');
$as = 'unisharp.lfm.';
$namespace = '\UniSharp\LaravelFilemanager\controllers';

// make sure authenticated
Route::group(compact('middleware', 'prefix', 'as', 'namespace'), function () {

    // Show LFM
    Route::get('/', [
        'uses' => 'LfmController@show',
        'as' => 'show',
    ]);

    // Show integration error messages
    Route::get('/errors', [
        'uses' => 'LfmController@getErrors',
        'as' => 'getErrors',
    ]);

    // upload
    Route::any('/upload', [
        'uses' => 'UploadController@upload',
        'as' => 'upload',
    ]);

    // list images & files
    Route::get('/jsonitems', [
        'uses' => 'ItemsController@getItems',
        'as' => 'getItems',
    ]);

    // folders
    Route::get('/newfolder', [
        'uses' => 'FolderController@getAddfolder',
        'as' => 'getAddfolder',
    ]);

    // list folders
    Route::get('/folders', [
        'uses' => 'FolderController@getFolders',
        'as' => 'getFolders',
    ]);

    // crop
    Route::get('/crop', [
        'uses' => 'CropController@getCrop',
        'as' => 'getCrop',
    ]);
    Route::get('/cropimage', [
        'uses' => 'CropController@getCropimage',
        'as' => 'getCropimage',
    ]);
    Route::get('/cropnewimage', [
        'uses' => 'CropController@getNewCropimage',
        'as' => 'getCropimage',
    ]);

    // rename
    Route::get('/rename', [
        'uses' => 'RenameController@getRename',
        'as' => 'getRename',
    ]);

    // scale/resize
    Route::get('/resize', [
        'uses' => 'ResizeController@getResize',
        'as' => 'getResize',
    ]);
    Route::get('/doresize', [
        'uses' => 'ResizeController@performResize',
        'as' => 'performResize',
    ]);

    // download
    Route::get('/download', [
        'uses' => 'DownloadController@getDownload',
        'as' => 'getDownload',
    ]);

    // delete
    Route::get('/delete', [
        'uses' => 'DeleteController@getDelete',
        'as' => 'getDelete',
    ]);

    Route::get('/demo', 'DemoController@index');
});

Route::get('/' . config('lfm.url_prefix') . '/' . config('lfm.images_folder_name') . '/{file_path}', $namespace . '\RedirectController@showFile')->where('file_path', '.*');
