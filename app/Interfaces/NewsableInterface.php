<?php

namespace App\Interfaces;

use Illuminate\Support\Collection;

interface NewsableInterface
{

    /**
     * make news model that can be inserted in db
     * 
     * @param Collection $news raw news data
     * 
     * @return Collection<App\Models\News> collection of structured news data
     */
    public function makeNewsModel(Collection $news): Collection;
}
