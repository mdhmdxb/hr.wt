<?php

return [

    'paths' => [
        resource_path('views'),
        base_path('Modules/Core/Resources/views'),
        base_path('Modules/Settings/Resources/views'),
        base_path('Modules/Employee/Resources/views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

];
