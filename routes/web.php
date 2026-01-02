<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;


Route::get('/', [TitleController::class, 'index']);
Route::get('/create', [TitleController::class, 'create']);
Route::post('/store', [TitleController::class, 'store']);

Route::get('/edit/{id}', [TitleController::class, 'editPage']);
Route::post('/update/{id}', [TitleController::class, 'update']);

Route::get('/delete/{id}', [TitleController::class, 'destroy']);
