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

class NewsApiStrategy implements NewsApiStrategyInterface, NewsableInterface
{

    public function getAlias(): string
    {
        return 'newsApi';
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
        $endDate = $endDate ? $endDate->subDay() : now()->subDay();

        $data = [
            "from" => $startDate->format('Y-m-d\TH:i:s'),
            "to" => $endDate->format('Y-m-d\TH:i:s'),
            "language" => 'en', // just search in English news
            "sortBy" => 'publishedAt', //newest news first
            "pageSize" => 100, //maximum
            "domains" => "news.google.com,cnn.com,ew.com,bbc.com,bbc.co.uk,techcrunch.com,engadget.com,androidcentral.com,thenextweb.com,gizmodo.com,businessinsider.com,wired.com,readwrite.com", // static domains, if we delete this line, we get an error (too broad search)
            // 'page' => 2 // page can not be specified because i don't have paid plan
        ];


        $response = $this->sendRequest('everything', $data, HttpMethodEnum::get);
        $body = $response->json();

        if (!$response->successful() || $body['status'] != 'ok') {
            Log::error('wrong answer from NewsApi API', ["body" => $body, 'trace' => debug_backtrace()]);
            throw new Exception('wrong answer from NewsApi');
        }

        return collect($body['articles']);
    }

    public function checkValidData(array $news): bool
    {
        // some news can be fetched in this API but all of its field has value of [Removed]
        $flag =  isset($news['url']) &&
            isset($news['title']) &&
            isset($news['description']) &&
            isset($news['content']) &&
            isset($news['urlToImage']) &&
            isset($news['author']) &&
            isset($news['publishedAt']) &&
            $news['url'] != '[Removed]';

        if (!$flag) return $flag;
        
        $id = $this->generateId($news);
        $checkUnique = News::query()->where('source_id', $id)->count();

        return !$checkUnique;
    }

    public function makeNewsModel(array $news): News
    {
        $id = $this->generateId($news);

        $newsModel = new News();
        $newsModel->source_id = $id;
        $newsModel->title = $news['title'];
        $newsModel->summary = $news['description'];
        $newsModel->body = $news['content'];
        $newsModel->image = $news['urlToImage'];
        $newsModel->author = $news['author'];
        $newsModel->source = isset($news['source']) ? $news['source']['name'] : $this->getAlias();
        $newsModel->section_name = null;
        $newsModel->source_url = $news['url'];
        $newsModel->published_at = Carbon::parse($news['publishedAt']);

        return $newsModel;
    }

    private function generateId(array $news): string
    {
        $url = trim($news['url'], ' /');
        $baseUrl = parse_url($url, PHP_URL_HOST) . '/';
        $id = substr($url, strrpos($url, $baseUrl) + strlen($baseUrl));

        return $id;
    }
}
