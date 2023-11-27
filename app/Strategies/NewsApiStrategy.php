<?php

namespace App\Strategies;

use App\Enums\HttpMethodEnum;
use App\Interfaces\NewsableInterface;
use App\Interfaces\NewsApiStrategyInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewsApiStrategy implements NewsApiStrategyInterface, NewsableInterface
{

    public function getAlias(): string
    {
        return 'newApi';
    }

    private function sendRequest(string $action, array $data, HttpMethodEnum $method = HttpMethodEnum::post): Response
    {
        return Http::acceptJson()
            ->baseUrl('https://newsapi.org/v2/')
            ->withHeaders([
                "X-Api-Key" => config('news.apiKeys.NewsApi')
            ])
            ->{$method->value}($action, $data);
    }

    public function getUpdatedNews(Carbon $startDate, ?Carbon $endDate = null): Collection
    {

        /**
         * in developer plan the news is only available after 24 hours
         * so we dynamically subDay to get latest news available for this plan
         */
        $startDate = $startDate->subDay();
        $endDate = $endDate ? $startDate->subDay() : now()->subDay();

        $data = [
            "from" => $startDate->format('Y-m-d\TH:i:s'),
            "to" => $endDate->format('Y-m-d\TH:i:s'),
            "language" => 'en', // just search in English news
            "sortBy" => 'publishedAt', //newest news first
            "pageSize" => 100,
            "domains" => "news.google.com,cnn.com,ew.com,bbc.com,bbc.co.uk,techcrunch.com,engadget.com,androidcentral.com,thenextweb.com,gizmodo.com,businessinsider.com,wired.com,readwrite.com", // static domains, if we delete this line, we get an error (too broad search)
            // 'page' => 2 // page can not be specified because i don't have paid plan
        ];


        $response = $this->sendRequest('everything', $data, HttpMethodEnum::get);
        $body = $response->json();

        if (!$response->successful() || $body['status'] != 'ok')
            throw new Exception('wrong answer from NewsApi');

        return collect($body['articles']);
    }

    public function makeNewsModel(Collection $news): Collection
    {
        return collect([]); //TODO: make news models
    }
}
