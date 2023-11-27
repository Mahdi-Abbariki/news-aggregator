<?php

use App\Strategies\GuardianStrategy;
use App\Strategies\NewsApiStrategy;
use App\Strategies\NYTimesStrategy;

return [
    /**
     * these classes must implement NewsApiStrategyInterface & NewsableInterface
     */
    'strategies' => [
        GuardianStrategy::class,
        NewsApiStrategy::class,
        NYTimesStrategy::class,
        // ...
        // add new strategies when its implemented
    ],

    'apiKeys' => [
        "NewsApi" => env('NEWS_API_API_KEY'),
        "NYTimes" => env('NYTIMES_API_KEY'),
        "Guardian" => env('GUARDIAN_API_KEY'),
    ]
];
