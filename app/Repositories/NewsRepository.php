<?php

namespace App\Repositories;

use App\Interfaces\NewsableInterface;
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
    public static function updateNews(?Carbon $startDate = null, ?Carbon $endDate = null): void
    {
        $startDate = $startDate ?: now()->subHour();
        $endDate = $endDate ?: now();

        $strategies = config('news.strategies');
        $classes = [];
        $rawNews = [];

        foreach ($strategies as $newsStrategy) {
            if (class_exists($newsStrategy)) {
                $newsStrategy = new $newsStrategy();

                $classes[$newsStrategy->getAlias()] = $newsStrategy;

                $rawNews[$newsStrategy->getAlias()] = [];
                if ($newsStrategy instanceof NewsApiStrategyInterface && $newsStrategy instanceof NewsableInterface)
                    $rawNews[$newsStrategy->getAlias()] = $newsStrategy->getUpdatedNews($startDate, $endDate);
            }
        }

        foreach ($rawNews as $alias => $news) {
            $class = $classes[$alias];
            foreach ($news as $n)
                if ($class->checkValidData($n))
                    $class->makeNewsModel($n)->save();
        }
    }
}
