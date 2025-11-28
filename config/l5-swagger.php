<?php

return [

    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Juicebox API Documentation',
            ],

            'routes' => [
                'api' => 'api/documentation',
            ],

            'paths' => [
                'docs' => storage_path('api-docs'),
                'docs_json' => 'api-docs.json',
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],

    'defaults' => [

        'routes' => [
            'api' => 'api/documentation',
            'docs' => 'docs',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
            ],
        ],

        'paths' => [
            'docs' => storage_path('api-docs'),
            'docs_json' => 'api-docs.json',
            'annotations' => [
                base_path('app'),
            ],
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => null,
            'excludes' => [],
        ],

        'security' => [
            'sanctum' => [
                'type' => 'apiKey',
                'name' => 'Authorization',
                'in' => 'header',
            ],
        ],

        'generate_always' => false,
        'generate_yaml_copy' => false,
        'proxy' => false,

        'operations_sort' => 'alpha',
        'additional_config_url' => null,
        'validator_url' => null,

        'oauth2' => [
            'enabled' => false,
        ],
    ],
];
