<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\ProviderController as ProviderV1;
use App\Http\Controllers\Api\V1\BrandController as BrandV1;
use App\Http\Controllers\Api\V1\ArticleController as ArticleV1;
use App\Http\Controllers\Api\V1\CustomerController as CustomerV1;
use App\Http\Controllers\Api\V1\OrderController as OrderV1;
use App\Http\Controllers\Api\V1\CommitmentController as CommitmentV1;
use App\Http\Controllers\Api\V1\PaymentController as PaymentV1;
use App\Http\Controllers\Api\V1\GroupSizeController as GroupSizeV1;
use App\Http\Controllers\Api\V1\UserController as UserV1;
use App\Http\Controllers\Api\V1\VendorPeriodController as VendorPeriodV1;
use App\Http\Controllers\Api\V1\VendorInventoryController as VendorInventoryV1;
use App\Http\Controllers\Api\V1\VendorCustomerController as VendorCustomerV1;
use App\Http\Controllers\Api\V1\VendorPaymentController as VendorPaymentV1;
use App\Http\Controllers\Api\V1\VendorAssignmentController as VendorAssignmentV1;
use App\Http\Controllers\Api\V1\VendorCustomerPaymentController as VendorCustomerPaymentV1;
use App\Http\Controllers\Api\LoginController as LoginController;
// use App\Models\User as UserV1;
// use Illuminate\Support\Facades\Hash;

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

Route::middleware('auth:sanctum')->group(function () {

      Route::get('/user', function (Request $request) {
            return $request->user();
      });

      Route::delete('/sessions', [LoginController::class, 'logout']);

      Route::apiResource('v1/providers', ProviderV1::class)
            ->only(['index', 'show', 'destroy']);

      Route::apiResource('v1/brands', BrandV1::class);

      Route::apiResource('v1/articles', ArticleV1::class);
      Route::get('v1/article-data', [ArticleV1::class, 'article_data']);

      Route::get('v1/find_article_by_name', [ArticleV1::class, 'find_by_name']);
      Route::get('v1/find_article_by_code', [ArticleV1::class, 'find_by_code']);
      Route::put('v1/update_inventory', [ArticleV1::class, 'updateArticleSizeInInventory']);

      Route::apiResource('v1/customers', CustomerV1::class);

      Route::get('v1/customers/{id}/commitments', [CustomerV1::class, 'commitments_by_user']);

      Route::post('v1/customers/{id}/payments', [CustomerV1::class, 'create_payment']);

      Route::apiResource('v1/orders', OrderV1::class);

      Route::get('v1/orders_by_status', [OrderV1::class, 'orders_by_status']);

      Route::post('v1/orders/{id}/create_offer_and_add_offer', [OrderV1::class, 'add_offer_to_order']);
      Route::post('v1/orders/{id}/add_product', [OrderV1::class, 'add_product_to_order']);

      Route::put('v1/orders/{id}/update_detail/{order_detail_id}', [OrderV1::class, 'update_detail']);

      Route::delete('v1/orders/{id}/remove_product/{order_detail_id}', [OrderV1::class, 'remove_detail_item']);

      Route::put('v1/orders/{id}/confirm_order', [OrderV1::class, 'confirm_order']);


      Route::apiResource('v1/commitments', CommitmentV1::class);

      Route::get('v1/commitments_grouped_by_user', [CommitmentV1::class, 'commitments_grouped_by_user']);

      Route::get('v1/dashboard_data', [CommitmentV1::class, 'dashboard_data']);
      Route::get('v1/products_without_stock', [ArticleV1::class, 'products_without_stock']);

      Route::get('v1/commitments_by_date', [CommitmentV1::class, 'commitments_by_date']);

      Route::get('v1/generate_qrcode', [GroupSizeV1::class, 'generate_qrcode']);

      Route::get('v1/commitments_grouped_by_user_general', [CommitmentV1::class, 'commitments_grouped_by_user_general']);

      Route::get('v1/commitments/{id}/payments', [CommitmentV1::class, 'payments_to_commitments']);



      Route::apiResource('v1/payments', PaymentV1::class)
            ->only(['index', 'show', 'store']);

      Route::apiResource('v1/group_sizes', GroupSizeV1::class)
            ->only(['show', 'index']);

      // User routes
      Route::get('v1/users/vendors', [UserV1::class, 'vendors']);
      Route::post('v1/users', [UserV1::class, 'store']);
      Route::put('v1/users/{id}/printer-tunnel-url', [UserV1::class, 'updatePrinterTunnelUrl']);
      Route::put('v1/user/printer-tunnel-url', [UserV1::class, 'updateMyPrinterTunnelUrl']);

      // Vendor Period routes
      Route::get('v1/vendor-periods', [VendorPeriodV1::class, 'index']);
      Route::get('v1/vendor-periods/{id}', [VendorPeriodV1::class, 'show']);
      Route::post('v1/vendor-periods', [VendorPeriodV1::class, 'store']);
      Route::put('v1/vendor-periods/{id}/close', [VendorPeriodV1::class, 'close']);

      // Vendor Inventory routes
      Route::get('v1/vendor-inventory/period/{period_id}', [VendorInventoryV1::class, 'byPeriod']);
      Route::post('v1/vendor-inventory/assign', [VendorInventoryV1::class, 'assign']);
      Route::post('v1/vendor-inventory/return', [VendorInventoryV1::class, 'returnProducts']);

      // Vendor Customer routes
      Route::get('v1/vendor-customers/vendor/{vendor_id}/with-balance', [VendorCustomerV1::class, 'withBalance']);
      Route::get('v1/vendor-customers/vendor/{vendor_id}', [VendorCustomerV1::class, 'byVendor']);
      Route::get('v1/vendor-customers/eligible/{vendor_id}', [VendorCustomerV1::class, 'eligible']);
      Route::post('v1/vendor-customers', [VendorCustomerV1::class, 'store']);
      Route::delete('v1/vendor-customers/{id}', [VendorCustomerV1::class, 'destroy']);

      // Vendor Payment routes (vendor → admin)
      Route::get('v1/vendor-payments/period/{period_id}', [VendorPaymentV1::class, 'byPeriod']);
      Route::get('v1/vendor-payments/period/{period_id}/balance', [VendorPaymentV1::class, 'balance']);
      Route::post('v1/vendor-payments', [VendorPaymentV1::class, 'store']);

      // Vendor Assignment routes (vendor assigns garments to customers)
      Route::get('v1/vendor-assignments/period/{period_id}', [VendorAssignmentV1::class, 'byPeriod']);
      Route::post('v1/vendor-assignments', [VendorAssignmentV1::class, 'store']);
      Route::delete('v1/vendor-assignments/{id}', [VendorAssignmentV1::class, 'destroy']);

      // Vendor Customer Payment routes (customer pays vendor)
      Route::get('v1/vendor-customer-payments/period/{period_id}', [VendorCustomerPaymentV1::class, 'byPeriod']);
      Route::post('v1/vendor-customer-payments', [VendorCustomerPaymentV1::class, 'store']);
});

Route::post('sessions', [LoginController::class, 'login']);
// Route::get('temporary-password-reset', function () {
//       $user = UserV1::where('email', 'edwinjoel1984@gmail.com')->first();
//       $user->password = Hash::make('123456');
//       $user->save();

//       return 'Success!';
// });
