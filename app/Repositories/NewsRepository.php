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
    public function updateNews(?Carbon $startDate = null, ?Carbon $endDate = null): void
    {
        $startDate = $startDate ?: now()->subHours(1);
        $endDate = $endDate ?: now();

        $strategies = config('news.strategies');
        $latestNewsModels = [];

        foreach ($strategies as $newsStrategy) {
            if (class_exists($newsStrategy)) {
                $newsStrategy = new $newsStrategy();
                if ($newsStrategy instanceof NewsApiStrategyInterface && $newsStrategy instanceof NewsableInterface) {
                    $rawNews = $newsStrategy->getUpdatedNews($startDate, $endDate);

                    foreach ($rawNews as $news)
                        if ($newsStrategy->checkValidData($news))
                            $latestNewsModels[] = $newsStrategy->makeNewsModel($news);
                }
            }
        }

        foreach ($latestNewsModels as $model)
            $model->save();
    }
}
