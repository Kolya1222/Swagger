<?php

use roilafx\swaggeruievo\Controllers\SwaggerViewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SwaggerViewController::class, '__invoke'])->name('swagger.index');