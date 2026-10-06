<?php

use App\Http\Middleware\VisitorMiddleware;
use App\Livewire\Pages\Home;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home')->middleware(VisitorMiddleware::class);
