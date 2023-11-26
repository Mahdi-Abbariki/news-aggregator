<?php

namespace App\Interfaces;

use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

interface NewsApiStrategyInterface
{
    /**
     * this function will be used to send the requests asynchronously through NewsRepository
     * 
     * @param DateTime $startDate 
     * @param DateTime $endDate it is optional, defaults to now() 
     * 
     * @return Collection $result
     */
    public function getUpdatedNews(DateTime $startDate, DateTime $endDate = null): Http;
}
