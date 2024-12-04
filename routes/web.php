<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentController;

// Existing routes
Route::get('/', function () {
    return view('moodify');
})->name('moodify');

Route::get('/analyze', function () {
    return view('moodify-analyze');
})->name('analyze');

Route::post('/analyze', [SentimentController::class, 'store'])->name('store');
Route::get('/history', [SentimentController::class, 'history'])->name('history');

// Route for soft deleting a sentiment
Route::delete('/sentiment-analysis/{id}/soft-delete', [SentimentController::class, 'softDelete'])->name('softDelete');

// Route for generating a report (adjust if needed)
Route::get('/moodify-report/{id}', [SentimentController::class, 'generateReport'])->name('generateReport');
