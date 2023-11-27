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
use Illuminate\Support\Facades\Log;

class NYTimesStrategy implements NewsApiStrategyInterface, NewsableInterface
{
    public function getAlias(): string
    {
        return 'newYorkTimes';
    }

    private function sendRequest(string $action, array $data, HttpMethodEnum $method = HttpMethodEnum::post): Response
    {
        $data['api-key'] = config('news.apiKeys.NYTimes');

        return Http::acceptJson()
            ->baseUrl('https://api.nytimes.com/svc/')
            ->{$method->value}($action, $data);
    }

    /**
     * New York Times does not accept hours (H:i:s) in its api
     * daily articles available in this API is around 80
     * so if we call it hourly and we check end date ourselves we can implement startDate and endDate
     */
    public function getUpdatedNews(Carbon $startDate, ?Carbon $endDate = null, int $page = 0): Collection
    {
        $perPage = 10; // 10 is mentioned in API documentation

        $endDate = $endDate ?: now();

        $data = [
            "begin_date" => now()->format('Ymd'),
            "end_date" => now()->format('Ymd'),
            "sort" => "newest",
            "page" => $page
        ];

        $response = $this->sendRequest('/search/v2/articlesearch.json?', $data, HttpMethodEnum::get);
        $body = $response->json(); // get all news for today

        if (!$response->successful() || $body['status'] != 'OK' || !isset($body['response']) || !isset($body['response']['docs']) || !isset($body['response']['meta'])) {
            Log::error('wrong answer from NYTimes API', ["body" => $body, 'trace' => debug_backtrace()]);
            throw new Exception('wrong answer from NYTimes API');
        }

        $articles = collect($body['response']['docs']);
        $paginationInfo = $body['response']['meta'];

        if (count($articles)) {
            //data is sorted by latest
            $latestArticle = $articles[0];
            $latestArticleDate = Carbon::parse($latestArticle['pub_date']);

            if ($startDate->gt($latestArticleDate)) // we currently have latest news stored in db
                return collect();

            $oldestArticle = $articles[count($articles) - 1];
            $oldestArticleDate = Carbon::parse($oldestArticle['pub_date']);

            if ($startDate->lt($oldestArticleDate) && $paginationInfo['offset'] + $perPage < $paginationInfo['hits']) { // we need to do pagination
                sleep(12); // sleep between each request to prevent RateLimit
                $nextPage = $this->getUpdatedNews($startDate, $endDate, $page + 1);
                $articles = $articles->concat($nextPage);
            }
        }


        return $articles->filter(fn ($article) => $startDate->lte(Carbon::parse($article['pub_date'])) && $endDate->gte(Carbon::parse($article['pub_date'])));
    }

    public function makeNewsModel(Collection $news): Collection
    {
        return collect([]); //TODO: make news models
    }
}
