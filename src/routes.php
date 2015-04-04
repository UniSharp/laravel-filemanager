<?php

Route::get('sample-ckeditor-integration', function(){
    return \Illuminate\Support\Facades\View::make('editor');
});

// Show LFM
Route::get('/laravel-filemanager', 'Tsawler\Laravelfilemanager\controllers\LfmController@show');


// upload
Route::any('/laravel-filemanager/upload', 'Tsawler\Laravelfilemanager\controllers\UploadController@upload');

// list images & files
Route::get('/laravel-filemanager/jsonimages', 'Tsawler\Laravelfilemanager\controllers\ItemsController@getImages');
Route::get('/laravel-filemanager/jsonfiles', 'Tsawler\Laravelfilemanager\controllers\ItemsController@getFiles');

// folders
Route::get('/laravel-filemanager/newfolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getAddfolder');
Route::get('/laravel-filemanager/deletefolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getDeletefolder');
Route::get('/laravel-filemanager/folders', 'Tsawler\Laravelfilemanager\controllers\FolderController@getFolders');

// crop
Route::get('/laravel-filemanager/crop', 'Tsawler\Laravelfilemanager\controllers\CropController@getCrop');
Route::get('/laravel-filemanager/cropimage', 'Tsawler\Laravelfilemanager\controllers\CropController@getCropimage');

// rename
Route::get('/laravel-filemanager/rename', 'Tsawler\Laravelfilemanager\controllers\RenameController@getRename');

// scale/resize
Route::get('/laravel-filemanager/resize', 'Tsawler\Laravelfilemanager\controllers\ResizeController@getResize');
Route::get('/laravel-filemanager/doresize', 'Tsawler\Laravelfilemanager\controllers\ResizeController@performResize');

// download
Route::get('/laravel-filemanager/download', 'Tsawler\Laravelfilemanager\controllers\DownloadController@getDownload');

// delete
Route::get('/laravel-filemanager/delete', 'Tsawler\Laravelfilemanager\controllers\DeleteController@getDelete');
