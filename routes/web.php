<?php

use App\Http\Controllers\DocumentController;
use Illuminate\Support\Facades\Route;

// Redirect the root URL to the documents page for convenience.
Route::get('/', function () {
    return redirect()->route('documents.index');
});

// Route to display the main page (list and upload form).
Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');

// Route to handle the form submission for file uploads.
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');

Route::get('/test-get', [DocumentController::class, 'testGetFile']);

Route::get('/debug-s3', function () {
    // IMPORTANT: Clear config cache right before testing
    \Artisan::call('config:clear');

    $config = config('filesystems.disks.s3');

    // Use dd() to stop execution and display the configuration.
    // 'dd' stands for 'dump and die'.
    dd("Laravel is using this S3 Configuration:", $config);
});
