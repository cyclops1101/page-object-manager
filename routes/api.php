<?php

use Illuminate\Support\Facades\Route;
use Cyclops1101\PageObjectManager\Http\Controllers\ActionController;
use Cyclops1101\PageObjectManager\Http\Controllers\CardController;
use Cyclops1101\PageObjectManager\Http\Controllers\FilterController;
use Cyclops1101\PageObjectManager\Http\Controllers\LensController;
use Cyclops1101\PageObjectManager\Http\Controllers\Page\IndexController as PageResourceIndexController;
use Cyclops1101\PageObjectManager\Http\Controllers\Page\CountController as PageResourceCountController;
use Cyclops1101\PageObjectManager\Http\Controllers\Page\UpdateController as PageResourceUpdateController;
use Cyclops1101\PageObjectManager\Http\Controllers\Block\IndexController as BlockResourceIndexController;
use Cyclops1101\PageObjectManager\Http\Controllers\Block\CountController as BlockResourceCountController;
use Cyclops1101\PageObjectManager\Http\Controllers\Block\UpdateController as BlockResourceUpdateController;

/*
|--------------------------------------------------------------------------
| Tool API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your tool. These routes
| are loaded by the ServiceProvider of your tool. They are protected
| by your tool's "Authorize" middleware by default. Now, go build!
|
*/

// Actions...
Route::get('/nova-api/nova-page/actions', ActionController::class . '@index');
Route::get('/nova-api/nova-block/actions', ActionController::class . '@index');

// Filters...
Route::get('/nova-api/nova-page/filters', FilterController::class . '@index');
Route::get('/nova-api/nova-block/filters', FilterController::class . '@index');

// Lenses...
Route::get('/nova-api/nova-page/lenses', LensController::class . '@index');
Route::get('/nova-api/nova-block/lenses', LensController::class . '@index');

// Cards / Metrics...
Route::get('/nova-api/nova-page/cards', CardController::class . '@index');
Route::get('/nova-api/nova-block/cards', CardController::class . '@index');

// Resource Management...
Route::get('/nova-api/nova-page', PageResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-page/count', PageResourceCountController::class . '@show');
Route::put('/nova-api/nova-page/{resourceId}', PageResourceUpdateController::class . '@handle');

Route::get('/nova-api/nova-block', BlockResourceIndexController::class . '@handle');
Route::get('/nova-api/nova-block/count', BlockResourceCountController::class . '@show');
Route::put('/nova-api/nova-block/{resourceId}', BlockResourceUpdateController::class . '@handle');
