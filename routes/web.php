<?php

use Illuminate\Support\Facades\Route;

// Home page
Route::get('/moodify', function () {
    return view('moodify'); // Replace with the appropriate view name if necessary
})->name('moodify');


// Analyze Sentiments page
Route::get('/analyze', function () {
    return view('moodify-analyze'); // Modify Analyze Page
})->name('analyze');

// History page
Route::get('/history', function () {
    return view('moodify-history'); // Moodify History Page
})->name('history');


