<?php
// make sure authenticated
Route::group(array('middleware' => \Config::get('lfm.middlewares')), function ()
{

    Route::get('sample-ckeditor-integration', function () {
        return \Illuminate\Support\Facades\View::make('editor');
    });

    // Show LFM
    Route::get('/laravel-filemanager', 'Unisharp\Laravelfilemanager\controllers\LfmController@show');


    // upload
    Route::any('/laravel-filemanager/upload', 'Unisharp\Laravelfilemanager\controllers\UploadController@upload');

    // list images & files
    Route::get('/laravel-filemanager/jsonimages', 'Unisharp\Laravelfilemanager\controllers\ItemsController@getImages');
    Route::get('/laravel-filemanager/jsonfiles', 'Unisharp\Laravelfilemanager\controllers\ItemsController@getFiles');

    // folders
    Route::get('/laravel-filemanager/newfolder', 'Unisharp\Laravelfilemanager\controllers\FolderController@getAddfolder');
    Route::get('/laravel-filemanager/deletefolder', 'Unisharp\Laravelfilemanager\controllers\FolderController@getDeletefolder');
    Route::get('/laravel-filemanager/folders', 'Unisharp\Laravelfilemanager\controllers\FolderController@getFolders');

    // crop
    Route::get('/laravel-filemanager/crop', 'Unisharp\Laravelfilemanager\controllers\CropController@getCrop');
    Route::get('/laravel-filemanager/cropimage', 'Unisharp\Laravelfilemanager\controllers\CropController@getCropimage');

    // rename
    Route::get('/laravel-filemanager/rename', 'Unisharp\Laravelfilemanager\controllers\RenameController@getRename');

    // scale/resize
    Route::get('/laravel-filemanager/resize', 'Unisharp\Laravelfilemanager\controllers\ResizeController@getResize');
    Route::get('/laravel-filemanager/doresize', 'Unisharp\Laravelfilemanager\controllers\ResizeController@performResize');

    // download
    Route::get('/laravel-filemanager/download', 'Unisharp\Laravelfilemanager\controllers\DownloadController@getDownload');

    // delete
    Route::get('/laravel-filemanager/delete', 'Unisharp\Laravelfilemanager\controllers\DeleteController@getDelete');

});