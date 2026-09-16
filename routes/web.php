<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/mahasiswa/dashboard');
});

Route::get('/mahasiswa/dashboard', function () {
    return view('mahasiswa.dashboard');
})->name('mahasiswa.dashboard');

Route::get('/bendahara/dashboard', function () {
    return view('bendahara.dashboard');
})->name('bendahara.dashboard');
