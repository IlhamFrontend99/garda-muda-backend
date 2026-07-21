<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating engines have a compiled view, meaning they are compiled
    | into plain PHP. Here you may specify the directory where compiled
    | views will be stored for your application.
    |
    */

    "paths" => [
        resource_path("views"),
    ],

    "compiled" => env(
        "VIEW_COMPILED_PATH",
        "/tmp/framework/views"
    ),

];