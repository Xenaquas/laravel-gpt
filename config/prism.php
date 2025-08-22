<?php

return [
    'ollama' => [
        'host' =>  env('OLLAMA_URL', 'http://127.0.0.1:11434'),
        'timeout' => 120,
    ],
    
    'models' => [
        'default' => env('DEFAULT_MODEL', 'tinyllama'),
        'available' => [
            'tinyllama',
            'deepseek-r1',
            'gemma3:4b', 
            'mistral',
            'codellama'
        ]
    ]
];