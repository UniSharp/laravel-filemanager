<?php

Route::get('sample-ckeditor-integration', function(){
    return \Illuminate\Support\Facades\View::make('editor');
});

// general routes
Route::get('/laravel-filemanager', 'Tsawler\Laravelfilemanager\controllers\LfmController@show');
Route::post('/laravel-filemanager/upload', 'Tsawler\Laravelfilemanager\controllers\LfmController@upload');
Route::get('/laravel-filemanager/data', 'Tsawler\Laravelfilemanager\controllers\LfmController@getData');
Route::get('/laravel-filemanager/delete', 'Tsawler\Laravelfilemanager\controllers\LfmController@getDelete');

Route::get('/laravel-filemanager/jsonimages', 'Tsawler\Laravelfilemanager\controllers\LfmController@getImages');
Route::get('/laravel-filemanager/jsonfiles', 'Tsawler\Laravelfilemanager\controllers\LfmController@getFiles');

// folders
Route::get('/laravel-filemanager/newfolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getAddfolder');
Route::get('/laravel-filemanager/deletefolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getDeletefolder');

// crop
Route::get('/laravel-filemanager/crop', 'Tsawler\Laravelfilemanager\controllers\CropController@getCrop');
Route::get('/laravel-filemanager/cropimage', 'Tsawler\Laravelfilemanager\controllers\CropController@getCropimage');

// rename
Route::get('/laravel-filemanager/rename', 'Tsawler\Laravelfilemanager\controllers\RenameController@getRename');

// scale/resize
Route::get('/laravel-filemanager/scale', 'Tsawler\Laravelfilemanager\controllers\ScaleController@getScale');
