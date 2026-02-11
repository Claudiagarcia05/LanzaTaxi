<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['message' => 'LanzaTaxi API is running'];
});
