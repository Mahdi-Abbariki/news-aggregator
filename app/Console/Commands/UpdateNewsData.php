<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        //
    }
}
