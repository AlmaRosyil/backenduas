<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    CategoryController,
    ProductController,
    RoleController,
    UserController,
    UserRoleController,
    WarehouseController,
    WarehouseProductController,
    MerchantController,
    MerchantProductController,
    TransactionController
};

// =======================
// AUTH ROUTES
// =======================
Route::post('token-login', [AuthController::class, 'tokenLogin']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
});

// =======================
// ROUTES FOR MANAGER
// =======================
Route::middleware(['auth:sanctum', 'role:manager'])->group(function () {
    // User & Role
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::post('users/roles', [UserRoleController::class, 'assignRole']);

    // Categories & Products
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('products', ProductController::class);

    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);
    Route::post('warehouses/{warehouse}/products', [WarehouseProductController::class, 'attach']);
    Route::put('warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'update']);
    Route::delete('warehouses/{warehouse}/products/{product}', [WarehouseProductController::class, 'detach']);

    // Merchants
    Route::apiResource('merchants', MerchantController::class);
    Route::post('merchants/{merchant}/products', [MerchantProductController::class, 'store']);
    Route::put('merchants/{merchant}/products/{product}', [MerchantProductController::class, 'update']);
    Route::delete('merchants/{merchant}/products/{product}', [MerchantProductController::class, 'destroy']);

    // Transactions
    Route::apiResource('transactions', TransactionController::class);
});

// =======================
// ROUTES FOR MANAGER & KEEPER (READ-ONLY / LIMITED)
// =======================
Route::middleware(['auth:sanctum', 'role:manager|keeper'])->group(function () {
    // Read only
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('categories/{category}', [CategoryController::class, 'show']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{product}', [ProductController::class, 'show']);

    Route::get('warehouses', [WarehouseController::class, 'index']);
    Route::get('warehouses/{warehouse}', [WarehouseController::class, 'show']);

    // Transactions for keeper
    Route::post('transactions', [TransactionController::class, 'store']);
    Route::get('transactions/{transaction}', [TransactionController::class, 'show']);

    // My merchant
    Route::get('my-merchant', [MerchantController::class, 'getMyMerchantProfile']);
    Route::get('my-merchant/transactions', [TransactionController::class, 'getTransactionsByMerchant']);
});
