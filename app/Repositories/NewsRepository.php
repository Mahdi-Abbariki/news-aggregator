<?php

namespace App\Repositories;

use App\Interfaces\NewsApiStrategyInterface;
use Carbon\Carbon;

class NewsRepository
{
    /**
     * this api must be called at least daily
     * 
     * @param Carbon $startDate defaults to one hour ago
     * @param Carbon $endDate defaults to now
     */
    public function updateNews(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $startDate = $startDate ?: now()->subHour();
        $endDate = $endDate ?: now();

        $strategies = config('news.strategies');
        $latestNews = [];

        foreach ($strategies as $newsStrategy) {
            if (class_exists($newsStrategy)) {
                $newsStrategy = new $newsStrategy();
                if ($newsStrategy instanceof NewsApiStrategyInterface)
                    $latestNews[$newsStrategy->getAlias()] = $newsStrategy->getUpdatedNews($startDate, $endDate);
            }
        }

        dd($latestNews);
    }
}
