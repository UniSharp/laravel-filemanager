<?php

return [
    'rename_file'        => true,

    'use_package_routes' => true,

    'middlewares'        => ['auth'],

    'allow_multi_user'   => true,

    'user_field'         => 'id',

    'shared_folder_name' => 'shares',
    'thumb_folder_name'  => 'thumbs',

    'images_dir'         => 'public/photos/',
    'images_url'         => '/photos/',

    'files_dir'          => 'public/files/',
    'files_url'          => '/files/',

    'file_type_array'         => [
        'pdf'  => 'Adobe Acrobat',
        'docx' => 'Microsoft Word',
        'docx' => 'Microsoft Word',
        'xls'  => 'Microsoft Excel',
        'xls'  => 'Microsoft Excel',
        'zip'  => 'Archive',
        'gif'  => 'GIF Image',
        'jpg'  => 'JPEG Image',
        'jpeg' => 'JPEG Image',
        'png'  => 'PNG Image',
        'ppt'  => 'Microsoft PowerPoint',
        'pptx' => 'Microsoft PowerPoint',
    ],

    'file_icon_array'         => [
        'pdf'  => 'fa-file-pdf-o',
        'docx' => 'fa-file-word-o',
        'docx' => 'fa-file-word-o',
        'xls'  => 'fa-file-excel-o',
        'xls'  => 'fa-file-excel-o',
        'zip'  => 'fa-file-archive-o',
        'gif'  => 'fa-file-image-o',
        'jpg'  => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'png'  => 'fa-file-image-o',
        'ppt'  => 'fa-file-powerpoint-o',
        'pptx' => 'fa-file-powerpoint-o',
    ],
];
