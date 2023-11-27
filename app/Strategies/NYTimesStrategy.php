<?php

namespace App\Strategies;

use App\Enums\HttpMethodEnum;
use App\Interfaces\NewsableInterface;
use App\Interfaces\NewsApiStrategyInterface;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NYTimesStrategy implements NewsApiStrategyInterface, NewsableInterface
{
    public function getAlias(): string
    {
        return 'newYorkTimes';
    }

    private function sendRequest(string $action, array $data, HttpMethodEnum $method = HttpMethodEnum::post): Response
    {
        return Http::acceptJson()
            ->baseUrl('')
            ->{$method->value}($action, $data);
    }

    public function getUpdatedNews(Carbon $startDate, ?Carbon $endDate = null): Collection
    {
        return collect([]);
    }

    public function makeNewsModel(Collection $news): Collection
    {
        return collect([]); //TODO: make news models
    }
}
