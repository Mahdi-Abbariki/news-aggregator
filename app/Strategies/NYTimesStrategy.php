<?php

namespace App\Strategies;

use App\Enums\HttpMethodEnum;
use App\Interfaces\NewsableInterface;
use App\Interfaces\NewsApiStrategyInterface;
use App\Models\News;
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

    public function checkValidData(array $news): bool
    {
        $flag = isset($news['uri']) &&
            isset($news['headline']) &&
            isset($news['headline']['main']) &&
            isset($news['abstract']) &&
            isset($news['lead_paragraph']) &&
            isset($news['multimedia']) &&
            is_array($news['multimedia']) &&
            count($news['multimedia']) &&
            isset($news['byline']) &&
            isset($news['byline']['original']) &&
            isset($news['section_name']) &&
            isset($news['web_url']) &&
            isset($news['pub_date']);

        if (!$flag) return $flag;

        $checkUnique = News::query()->where('source_id', $news['uri'])->count();

        return !$checkUnique;
    }

    public function makeNewsModel(array $news): News
    {
        $image = null;
        foreach ($news['multimedia'] as $multimedia)
            if ($multimedia['type'] == 'image' && in_array($multimedia['subType'], ['thumbnail', 'thumbLarge', 'wide']))
                $image = 'https://www.nytimes.com/' . $multimedia['url'];

        $newsModel = new News();
        $newsModel->source_id = $news['uri'];
        $newsModel->title = $news['headline']['main'];
        $newsModel->summary = $news['abstract'];
        $newsModel->body = $news['lead_paragraph']; // NYTimes API does not provide getting whole content of a news
        $newsModel->image = $image;
        $newsModel->author = $news['byline']['original'];
        $newsModel->source = isset($news['source']) ? $news['source'] : $this->getAlias();
        $newsModel->section_name = $news['section_name'] . (isset($news['subsection_name']) ?: '');
        $newsModel->source_url = $news['web_url'];
        $newsModel->published_at = Carbon::parse($news['pub_date']);

        return $newsModel;
    }
}
