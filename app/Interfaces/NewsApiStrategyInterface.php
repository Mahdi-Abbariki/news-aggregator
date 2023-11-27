<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface NewsApiStrategyInterface
{
    /** 
     * @param Carbon $startDate 
     * @param Carbon $endDate it is optional, defaults to now() 
     * 
     * @return Collection $result
     */
    public function getUpdatedNews(Carbon $startDate, Carbon $endDate = null): Collection;
}
