<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ProviderController as ProviderV1;
use App\Http\Controllers\Api\V1\BrandController as BrandV1;
use App\Http\Controllers\Api\V1\ArticleController as ArticleV1;
use App\Http\Controllers\Api\V1\CustomerController as CustomerV1;
use App\Http\Controllers\Api\V1\OrderController as OrderV1;
use App\Http\Controllers\Api\V1\CommitmentController as CommitmentV1;

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

Route::apiResource('v1/orders', OrderV1::class)
      ->middleware('auth:sanctum');

Route::get('v1/orders_by_status', [OrderV1::class, 'orders_by_status'])
      ->middleware('auth:sanctum');

Route::post('v1/orders/{id}/add_product', [OrderV1::class, 'add_product_to_order'])
      ->middleware('auth:sanctum');

Route::put('v1/orders/{id}/update_detail/{order_detail_id}', [OrderV1::class, 'update_detail'])
      ->middleware('auth:sanctum');

Route::delete('v1/orders/{id}/remove_product/{order_detail_id}', [OrderV1::class, 'remove_detail_item'])
      ->middleware('auth:sanctum');

Route::put('v1/orders/{id}/confirm_order', [OrderV1::class, 'confirm_order'])
      ->middleware('auth:sanctum');

Route::apiResource('v1/commitments', CommitmentV1::class)
      ->middleware('auth:sanctum');


Route::post('login', [App\Http\Controllers\Api\LoginController::class, 'login']);
