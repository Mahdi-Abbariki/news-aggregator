<?php

namespace App\Interfaces;

use App\Models\News;
use Illuminate\Support\Collection;

interface NewsableInterface
{

    /**
     * make news model that can be inserted in db
     * 
     * @param Collection $news raw news data
     * 
     * @return News final News Model
     */
    public function makeNewsModel(array $news): News;

    /**
     * check whether the raw data can be inserted as News Model
     * 
     * @param array $news
     * 
     * @param bool 
     */
    public function checkValidData(array $news): bool;
}
