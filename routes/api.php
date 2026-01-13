<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Constants\RoutePaths;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;

Route::post(RoutePaths::REGISTER, [AuthController::class, 'store']);
Route::post(RoutePaths::LOGIN, [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('user')->group(function () {
        Route::get(RoutePaths::INDEX, [UserController::class, 'index']);
        Route::post(RoutePaths::STORE, [UserController::class, 'store']);
        Route::put(RoutePaths::UPDATE, [UserController::class, 'update']);
        Route::delete(RoutePaths::DELETE, [UserController::class, 'delete']);
        Route::get('/profile', [UserController::class, 'profile'])->name('profile.fetch');
    });

    Route::prefix('post')->group(function () {
        Route::get(RoutePaths::INDEX, [PostController::class, 'index'])->name('posts.index');
        Route::get('/my', [PostController::class, 'myPosts'])->name('posts.my');
        Route::post(RoutePaths::STORE, [PostController::class, 'store'])->name('posts.store');
        Route::get(RoutePaths::SHOW, [PostController::class, 'show'])->name('posts.show');
        Route::put(RoutePaths::UPDATE, [PostController::class, 'update'])->name('posts.update');
        Route::delete(RoutePaths::DELETE, [PostController::class, 'destroy'])->name('posts.destroy');
    });

    Route::prefix('comment')->group(function () {
        Route::get('/post/{postId}', [CommentsController::class, 'index']);
        Route::post(RoutePaths::STORE, [CommentsController::class, 'store']);
        Route::put(RoutePaths::UPDATE, [CommentsController::class, 'update']);
        Route::delete(RoutePaths::DELETE, [CommentsController::class, 'destroy']);
    });

    Route::post(RoutePaths::LOGOUT, [AuthController::class, 'logout']);
});
