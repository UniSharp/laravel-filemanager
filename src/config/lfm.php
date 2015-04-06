<?php

return [
    'use_package_routes' => true,

    'images_dir'         => 'public/vendor/laravel-filemanager/images/',
    'images_url'         => '/vendor/laravel-filemanager/images/',

    'files_dir'          => 'public/vendor/laravel-filemanager/files/',
    'files_url'          => '/vendor/laravel-filemanager/files/',

    'params'             => 'type=Images&CKEditor=editor&CKEditorFuncNum=1&langCode=en',

    'type_array'         => [
        "pdf"  => "Adobe Acrobat",
        "docx" => "Microsoft Word",
        "docx" => "Microsoft Word",
        "xls"  => "Microsoft Excel",
        "xls"  => "Microsoft Excel",
        "zip"  => 'Archive',
        "gif"  => 'GIF Image',
        "jpg"  => 'JPEG Image',
        "jpeg" => 'JPEG Image',
        "png"  => 'PNG Image',
        "ppt"  => 'Microsoft PowerPoint',
        "pptx" => 'Microsoft PowerPoint',
    ],

    'icon_array'         => [
                                "pdf"  => "fa-file-pdf-o",
                                "docx" => "fa-file-word-o",
                                "docx" => "fa-file-word-o",
                                "xls"  => "fa-file-excel-o",
                                "xls"  => "fa-file-excel-o",
                                "zip"  => 'fa-file-archive-o',
                                "gif"  => 'fa-file-image-o',
                                "jpg"  => 'fa-file-image-o',
                                "jpeg" => 'fa-file-image-o',
                                "png"  => 'fa-file-image-o',
                                "ppt"  => 'fa-file-powerpoint-o',
                                "pptx" => 'fa-file-powerpoint-o',
                            ],
];