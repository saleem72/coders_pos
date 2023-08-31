<?php

use App\Http\Controllers\Admin\SuperAdminControoler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\V1\UnitController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InvoiceController;

Route::group(['prefix' => 'auth', 'namespace' => 'App\Http\Controllers\Auth'], function() {
    Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('email_verification', [AuthController::class, 'email_verification']);
    Route::post('resend_email_verification_code', [AuthController::class, 'resend_email_verification_code']);
    Route::post('request_password_reset', [AuthController::class, 'request_password_reset']);
    Route::post('reset_password', [AuthController::class, 'reset_password']);
});

Route::group([
    'prefix' =>  'v1/users',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('upload_image', [UserController::class, 'uploadImage']);
    Route::get('getUsers', [UserController::class, 'getUsers']);
});

Route::group([
    'prefix' =>  'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {
    Route::resource('categories', CategoryController::class);
});

Route::group([
    'prefix' =>  'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {
    Route::get('products/products_by_category', [ProductController::class, 'productsByCategory']);
    Route::post('products/update_products_image', [ProductController::class, 'updateProductImage']);
    Route::resource('products', ProductController::class);
});

Route::group([
    'prefix' =>  'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {
    Route::resource('units', UnitController::class);
});

Route::group([
    'prefix' =>  'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {
    Route::post('customers/update_customer_image', [CustomerController::class, 'updateCustomerImage']);
    Route::resource('customers', CustomerController::class);
});

Route::group([
    'prefix' =>  'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () { // invoicesForProduct
    Route::get('invoices/invoices_for_customer/{id}', [InvoiceController::class, 'invoicesForCustomer']);
    Route::get('invoices/invoices_for_product/{id}', [InvoiceController::class, 'invoicesForProduct']);
    Route::resource('invoices', InvoiceController::class);
});

Route::get('test', function() {
    return 'Test is ok';
});



Route::group([
    'prefix' =>  'admin',
    'namespace' => 'App\Http\Controllers\Api\Admin',
    'middleware' => ['auth:sanctum', 'role:superAdmin']
], function () { // invoicesForProduct
    Route::post('create_user', [SuperAdminControoler::class, 'createUser']);
});
