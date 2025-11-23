<?php

return [
    'lexicon' => 1,
    'id' => 'com.atproto.repo.getRecord',
    'description' => 'Get a record',
    'defs' => [
        'main' => [
            'type' => 'query',
            'parameters' => [
                'type' => 'params',
                'required' => ['repo', 'collection', 'rkey'],
                'properties' => [
                    'repo' => [
                        'type' => 'string',
                    ],
                    'collection' => [
                        'type' => 'string',
                    ],
                    'rkey' => [
                        'type' => 'string',
                    ],
                ],
            ],
        ],
    ],
];
