<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ProviderController as ProviderV1;
use App\Http\Controllers\Api\V1\BrandController as BrandV1;
use App\Http\Controllers\Api\V1\ArticleController as ArticleV1;
use App\Http\Controllers\Api\V1\CustomerController as CustomerV1;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
      return $request->user();
});

Route::apiResource('v1/providers', ProviderV1::class)
      ->only(['index', 'show', 'destroy'])
      ->middleware('auth:sanctum');

Route::apiResource('v1/brands', BrandV1::class)
      ->middleware('auth:sanctum');

Route::apiResource('v1/articles', ArticleV1::class)
      ->middleware('auth:sanctum');

Route::apiResource('v1/customers', CustomerV1::class)
      ->middleware('auth:sanctum');

Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);
