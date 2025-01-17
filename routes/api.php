<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function() {
    Route::get('list-articles', [App\Http\Controllers\API\v1\ArticleController::class, 'index']);
    Route::post('store-article', [App\Http\Controllers\API\v1\ArticleController::class, 'store']);
    Route::put('update-article/{id}', [App\Http\Controllers\API\v1\ArticleController::class, 'update']);
    Route::delete('delete-article/{id}', [App\Http\Controllers\API\v1\ArticleController::class, 'destroy']);
});
