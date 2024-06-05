<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Area;

//Route::get('/', function () {
//    return view('welcome');
//});


Route::get('/', Area::class);
