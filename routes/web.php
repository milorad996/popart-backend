<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/check-storage-link', function () {
    $linkPath = public_path('storage');
    if (file_exists($linkPath) && is_link($linkPath)) {
        return 'Storage link postoji i pokazuje na: ' . readlink($linkPath);
    } else {
        return 'Storage link ne postoji!';
    }
});

Route::get('/check-image', function () {
    $filePath = storage_path('app/public/listings/gbN95irAd7vps40s5iq5IwWeb7VK3NwHTbY9vO6o.jpg');
    if (file_exists($filePath)) {
        return response()->file($filePath);
    } else {
        return 'Slika ne postoji na putanji: ' . $filePath;
    }
});

