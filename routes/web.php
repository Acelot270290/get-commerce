<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/category/{category}', [ProductController::class, 'showCategory'])->name('category.show');
Route::get('/api/products/{category}', [ProductController::class, 'getProducts'])->name('api.products');
Route::post('/scrape-products', [ProductController::class, 'scrapeProducts'])->name('scrape.products');


