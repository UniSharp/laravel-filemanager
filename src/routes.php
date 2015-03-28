<?php


Route::get('/laravel-filemanager', 'Tsawler\Laravelfilemanager\controllers\LfmController@show');

Route::post('/laravel-filemanager/upload', 'Tsawler\Laravelfilemanager\controllers\LfmController@upload');
