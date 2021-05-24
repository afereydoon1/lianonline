<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'endpoint' => env('AWS_URL'),
        ],
        'product_image' => [
            'driver' => 'local',
            'root' => public_path('/uploads/product_images/'),
            'url' => env('APP_URL').'/uploads/product_images/',
            'visibility' => 'public',
        ],
        'users_images' => [
            'driver' => 'local',
            'root' => public_path('/uploads/users_images/'),
            'url' => env('APP_URL').'/uploads/users_images/',
            'visibility' => 'public',
        ],
        'post_images' => [
            'driver' => 'local',
            'root' => public_path('/uploads/post_images/'),
            'url' => env('APP_URL').'/uploads/post_images/',
            'visibility' => 'public',
        ],
        'slider_images' => [
            'driver' => 'local',
            'root' => public_path('/uploads/slider_images/'),
            'url' => env('APP_URL').'/uploads/slider_images/',
            'visibility' => 'public',
        ],
        'download_host' => [
            'driver' => 'ftp',
            'host' => '212.33.195.3',//env('DH_FTP_URL', 'localhost'),
            'username' => 'lianuploader@up.lianonline.ir',//env('DH_FTP_USERNAME', 'user'),
            'password' => 'zFbgFticE',//env('DH_FTP_PASSWORD', 'password'),
            'ssl' => false,

            // Optional FTP Settings...
            // 'port' => 21,
            // 'root' => '',
            // 'passive' => true,
            // 'timeout' => 30,
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
