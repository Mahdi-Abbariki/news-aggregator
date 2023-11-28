<?php

namespace App\Console\Commands;

use App\Repositories\NewsRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class UpdateNewsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fetch news from all APIs specified, aggregate them and store them in local db';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = 'updateNews/dates.json';
        $lastUpdateDates = null;
        if (Storage::fileExists($filePath)) {
            $lastUpdateDates = (array)json_decode(Storage::get($filePath));
            if (Carbon::parse($lastUpdateDates['lastUpdate'])->gte(now()->subHour()->subMinute()))
                return 0; // we already have the last updates, there is no need to call the command again
        }

        $startDate = $lastUpdateDates ? Carbon::parse($lastUpdateDates['lastUpdate']) : now()->subHour();
        $endDate = now();

        Storage::put($filePath, json_encode(['lastUpdate' => $endDate])); // update the last update time in json file

        NewsRepository::updateNews($startDate, $endDate);

        return 0;
    }
}
