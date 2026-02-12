<?php

use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
Route::get('/', function () {
    return ['message' => 'LanzaTaxi API is running'];
});
=======
// Redirecciones permanentes de URLs antiguas .html a nuevas rutas Laravel
Route::permanentRedirect('/cliente.html', '/cliente');
Route::permanentRedirect('/taxista.html', '/taxista');
Route::permanentRedirect('/admin.html', '/admin');
Route::permanentRedirect('/index.html', '/');

// Página principal (landing)
Route::get('/', function () {
    return view('index');
})->name('home');

// Panel de cliente
Route::get('/cliente', function () {
    return view('cliente');
})->name('cliente');

// Panel de taxista
Route::get('/taxista', function () {
    return view('taxista');
})->name('taxista');

// Panel de administrador
Route::get('/admin', function () {
    return view('admin');
})->name('admin');

>>>>>>> origin/master
