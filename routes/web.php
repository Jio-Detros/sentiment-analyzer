<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('moodify');
});

// Analyze Sentiments page
Route::get('/analyze', function () {
    return "Analyze Sentiments Page"; // Replace with the appropriate view later
})->name('analyze');

// History page
Route::get('/history', function () {
    return "View History Page"; // Replace with the appropriate view later
})->name('history');