<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', fn () => view('app'));

Route::get('/{any}', fn () => view('app'))
    ->where('any', '.*');
