<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeedBackController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post("user/login", [AdminController::class, 'login'])->name('user.login');
Route::delete('user/logout', [AdminController::class, 'logout'])->name("user.logout");

// User
Route::post("user/store", [AdminController::class, 'store'])->name('user.store');
Route::get("user/index", [AdminController::class, 'index'])->name('user.index');
Route::get('user/profile', [AdminController::class, 'getProfile'])->name("user.profile");
Route::put("user/update/{user}", [AdminController::class, 'update'])->name('user.update');
Route::put("user/{user}/change-password",[AdminController::class, 'updatePassword'])->name('user.update-password');
Route::delete("user/delete/{id}",[AdminController::class, 'destroy'])->name('user.destroy');

// Category
Route::post("category/store", [CategoryController::class, 'store'])->name('category.store');
Route::get("category/index", [CategoryController::class, 'index'])->name('category.index');
Route::put("category/update/{category}", [CategoryController::class, 'update'])->name('category.update');
Route::delete("category/delete/{id}",[CategoryController::class, 'destroy'])->name('category.destroy');

// Product
Route::post("product/store", [ProductController::class, 'store'])->name('product.store');
Route::get("product/index", [ProductController::class, 'index'])->name('product.index');
Route::post("product/update/{product}", [ProductController::class, 'update'])->name('product.update');
Route::get("product/show/{product}", [ProductController::class, 'show'])->name('product.show');
Route::delete("product/delete/{id}",[ProductController::class, 'destroy'])->name('product.destroy');

// Order Item
Route::post("order_item/store", [OrderItemController::class, 'store'])->name('order_item.store');
Route::get("order_item/index", [OrderItemController::class, 'index'])->name('order_item.index');
Route::delete("order_item/delete/{id}",[OrderItemController::class, 'destroy'])->name('order_item.destroy');

// Order Item
Route::post("order/store", [OrderController::class, 'store'])->name('order.store');
Route::get("order/index", [OrderController::class, 'index'])->name('order.index');
Route::get("order/show/{order}", [OrderController::class, 'show'])->name('order.show');
Route::delete("order/delete/{id}",[OrderController::class, 'destroy'])->name('order.destroy');

// Feedback
Route::post("feedback/store", [FeedBackController::class, 'store'])->name('feedback.store');
Route::get("feedback/index", [FeedBackController::class, 'index'])->name('feedback.index');


