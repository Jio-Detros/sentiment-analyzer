<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SentimentController;

// Home page
Route::get('/', function () {
    return view('moodify');
})->name('moodify');

// Show the form for analysis (GET)
Route::get('/analyze', function () {
    return view('moodify-analyze');
})->name('analyze');

// Analyze Sentiments page
Route::post('/analyze', [SentimentController::class, 'store'])->name('store');

// History page
Route::get('/history', function () {
    return view('moodify-history'); 
})->name('history');

// Report page
Route::get('/moodify-report/{id}', function ($id) {
    // Assuming you have a controller that handles this logic
    // Replace this with the controller logic if needed
    return view('moodify-report', ['id' => $id]);
})->name('moodify-report');
