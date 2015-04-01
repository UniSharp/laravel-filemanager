<?php

Route::get('sample-ckeditor-integration', function(){
    return \Illuminate\Support\Facades\View::make('editor');
});

Route::get('/laravel-filemanager', 'Tsawler\Laravelfilemanager\controllers\LfmController@show');
Route::post('/laravel-filemanager/upload', 'Tsawler\Laravelfilemanager\controllers\LfmController@upload');
Route::get('/laravel-filemanager/data', 'Tsawler\Laravelfilemanager\controllers\LfmController@getData');
Route::get('/laravel-filemanager/delete', 'Tsawler\Laravelfilemanager\controllers\LfmController@getDelete');
Route::get('/laravel-filemanager/picsjson', 'Tsawler\Laravelfilemanager\controllers\LfmController@getImages');

Route::get('/laravel-filemanager/newfolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getAddfolder');
Route::get('/laravel-filemanager/deletefolder', 'Tsawler\Laravelfilemanager\controllers\FolderController@getDeletefolder');

Route::get('/laravel-filemanager/crop', 'Tsawler\Laravelfilemanager\controllers\CropController@getCrop');
Route::get('/laravel-filemanager/cropimage', 'Tsawler\Laravelfilemanager\controllers\CropController@getCropimage');

Route::get('/laravel-filemanager/rename', 'Tsawler\Laravelfilemanager\controllers\RenameController@getRename');

Route::get('/laravel-filemanager/scale', 'Tsawler\Laravelfilemanager\controllers\ScaleController@getScale');
