<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AIController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::post('/ai/chat', [AIController::class, 'chat'])->name('ai.chat');
    Route::post('/ai/content', [AIController::class, 'generateContent'])->name('ai.content');
    Route::get('/image-generator', function () {
        return view('image-generator');
    })->name('image.generator');
    Route::post('/ai/image', [AIController::class, 'generateImage'])->name('ai.image');
    Route::post('/ai/convert-image', [AIController::class, 'convertImage'])->name('ai.convert-image');
    Route::post('/ai/blog-upload', [AIController::class, 'uploadBlog'])->name('ai.blog-upload');
    Route::post('/ai/standardize', [AIController::class, 'standardize'])->name('ai.standardize');
});

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
