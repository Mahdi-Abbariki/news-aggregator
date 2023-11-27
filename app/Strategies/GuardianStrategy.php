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

class GuardianStrategy implements NewsApiStrategyInterface, NewsableInterface
{
    public function getAlias(): string
    {
        return 'guardian';
    }

    private function sendRequest(string $action, array $data, HttpMethodEnum $method = HttpMethodEnum::post): Response
    {
        $data['api-key'] = config('news.apiKeys.Guardian');
        $data['format'] = 'json';

        return Http::acceptJson()
            ->baseUrl('http://content.guardianapis.com/')
            ->{$method->value}($action, $data);
    }

    public function getUpdatedNews(Carbon $startDate, ?Carbon $endDate = null, int $page = 1): Collection
    {

        $endDate = $endDate ?: now();
        $data = [
            "lang" => "en",
            "from-date" => $startDate->toIso8601String(),
            "to-date" => $endDate->toIso8601String(),
            "use-date" => 'published',
            "page-size" => 50, //maximum
            "page" => $page,
            "order-by" => 'newest',
            "show-fields" => implode(',', ['byline', 'trailText', 'publication', 'thumbnail', 'shortUrl', 'headline', 'body']),
            "show-elements" => implode(',', ['image']),
        ];
        $response = $this->sendRequest('search', $data, HttpMethodEnum::get);
        $body = $response->json()['response'];

        if (!$response->successful() || $body['status'] != 'ok') {
            Log::error('wrong answer from Guardian API', ["body" => $body, 'trace' => debug_backtrace()]);
            throw new Exception('wrong answer from GuardianApi');
        }

        $articles =  $body['total'] > 0 ? collect($body['results']) : collect();

        if (isset($body['pages']) && $body['currentPage'] < $body['pages']) { // get all data recursively based on pagination
            $nextPage = $this->getUpdatedNews($startDate, $endDate, $page + 1);
            $articles = $articles->concat($nextPage);
        }

        return $articles;
    }

    public function makeNewsModel(Collection $news): Collection
    {
        return collect([]); //TODO: make news models
    }
}
