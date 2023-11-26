<?php

namespace App\Strategies;

use App\Enums\HttpMethodsEnum;
use App\Interfaces\NewsableInterface;
use App\Interfaces\NewsApiStrategyInterface;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GuardianStrategy implements NewsApiStrategyInterface, NewsableInterface
{
    private static $baseUrl = '/';

    private function sendRequest(string $action, array $data, HttpMethodsEnum $method = 'post'): Http
    {
        return Http::acceptsJson()->{$method}(self::$baseUrl . $action, $data);
    }

    public function getUpdatedNews(DateTime $startDate, ?DateTime $endDate = null): Http
    {
        return $this->sendRequest('action', []);
    }

    public function makeNewsModel(Collection $news): Collection
    {
        return collect([]);
    }
}
