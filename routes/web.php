<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;

Route::get('/', [TitleController::class, 'index']);
Route::get('/create', [TitleController::class, 'create']);
Route::post('/store', [TitleController::class, 'store']);

Route::get('/edit/{id}', [TitleController::class, 'editPage']);
Route::post('/update/{id}', [TitleController::class, 'update']);

Route::get('/delete/{id}', [TitleController::class, 'destroy']);

Route::post('/bulk-delete', [TitleController::class, 'bulkDestroy']);

Route::get('/preview/{id}', [TitleController::class, 'preview']);

Route::post('/autosave/{id}', [TitleController::class, 'autosave']);

Route::post('/comment/{id}', [TitleController::class, 'addComment']);

Route::get('/restore/{id}', [TitleController::class, 'restore']);
Route::get('/force-delete/{id}', [TitleController::class, 'forceDelete']);
