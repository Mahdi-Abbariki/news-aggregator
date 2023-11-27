<?php

namespace App\Interfaces;

use Carbon\Carbon;
use Illuminate\Support\Collection;

interface NewsApiStrategyInterface
{

    /** 
     * get alias of the strategy, it can be used for key of array as it must be unique among strategies
     * 
     * @return string 
     */
    public function getAlias(): string;

    /** 
     * @param Carbon $startDate 
     * @param Carbon $endDate it is optional, defaults to now() 
     * 
     * @return Collection $result
     */
    public function getUpdatedNews(Carbon $startDate, Carbon $endDate = null): Collection;
}
