<?php

use App\Http\Controllers\NewsController;
use Illuminate\Support\Facades\Route;


Route::get('news', [NewsController::class, 'index']);
Route::get('sources', [NewsController::class, 'sourcesIndex']);
Route::get('sections', [NewsController::class, 'sectionsIndex']);
