<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TitleController;

/*
|--------------------------------------------------------------------------
| Title Routes
|--------------------------------------------------------------------------
*/

// List titles
Route::get('/', [TitleController::class, 'index']);

// Create
Route::get('/create', [TitleController::class, 'create']);
Route::post('/store', [TitleController::class, 'store']);

// Edit
Route::get('/edit/{id}', [TitleController::class, 'editPage']);

// Update
Route::post('/update/{id}', [TitleController::class, 'update']);

// Delete
Route::get('/delete/{id}', [TitleController::class, 'destroy']);

// Bulk delete
Route::post('/bulk-delete', [TitleController::class, 'bulkDestroy']);

// Preview
Route::get('/preview/{id}', [TitleController::class, 'preview']);

// Auto-save
Route::post('/autosave/{id}', [TitleController::class, 'autosave']);

// Comments
Route::post('/comment/{id}', [TitleController::class, 'addComment']);

// Restore soft deleted title
Route::get('/restore/{id}', [TitleController::class, 'restore']);

// Permanently delete
Route::get('/force-delete/{id}', [TitleController::class, 'forceDelete']);