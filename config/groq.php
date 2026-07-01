<?php

return [
    'api_key' => env('GROQ_API_KEY'),
    'base_url' => 'https://api.groq.com/openai/v1',
    'default_model' => env('GROQ_DEFAULT_MODEL', 'llama-3.3-70b-versatile'),
];
