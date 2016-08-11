<?php

return [
    // If true, the uploaded file will be renamed to uniqid() + file extension.
    'rename_file'           => true,

    // If rename_file set to false and this set to true, then non-alphanumeric characters in filename will be replaced.
    'alphanumeric_filename' => true,
    // If true, non-alphanumeric folder name will not be allowed.
    'alphanumeric_directory' => false,

    'use_package_routes'    => true,

    // For laravel 5.2, please set to ['web', 'auth']
    'middlewares'           => ['auth'],

    // Add prefix for routes
    'prefix'           => 'laravel-filemanager',

    // Allow multi_user mode or not.
    // If true, laravel-filemanager create private folders for each signed-in user.
    'allow_multi_user'      => true,

    // The database field to identify a user.
    // When set to 'id', the private folder will be named as the user id.
    // NOTE: make sure to use an unique field.
    'user_field'            => 'id',

    'shared_folder_name'    => 'shares',
    'thumb_folder_name'     => 'thumbs',

    'images_dir'            => 'public/photos/',
    'images_url'            => '/photos/',

    'files_dir'             => 'public/files/',
    'files_url'             => '/files/',

    // available since v1.3.0
    'valid_image_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif'
    ],

    // available since v1.3.0
    // only when '/laravel-filemanager?type=Files'
    'valid_file_mimetypes' => [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/gif',
        'application/pdf',
        'text/plain',
    ],

    // file extensions array, only for showing file information, it won't affect the upload process.
    'file_type_array'         => [
        'pdf'  => 'Adobe Acrobat',
        'doc'  => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls'  => 'Microsoft Excel',
        'xlsx' => 'Microsoft Excel',
        'zip'  => 'Archive',
        'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
        'ppt'  => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
    ],

    // file extensions array, only for showing icons, it won't affect the upload process.
    'file_icon_array'         => [
        'pdf'  => 'fa-file-pdf-o',
        'doc'  => 'fa-file-word-o',
        'docx' => 'fa-file-word-o',
        'xls'  => 'fa-file-excel-o',
        'xlsx' => 'fa-file-excel-o',
        'zip'  => 'fa-file-archive-o',
        'gif'  => 'fa-file-image-o',
        'jpg'  => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'png'  => 'fa-file-image-o',
        'ppt'  => 'fa-file-powerpoint-o',
        'pptx' => 'fa-file-powerpoint-o',
    ],
];
