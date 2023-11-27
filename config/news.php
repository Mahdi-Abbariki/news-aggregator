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
        "NewsApi" => env('NEWS_API_API_KEY'),
    ]
];
