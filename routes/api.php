<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\ObjectController;

Route::get('/images/latest', [ImageController::class, 'latest']);
Route::get('/objects/board', [ObjectController::class, 'boardApi']);
Route::get('/objects/{id}', [ObjectController::class, 'showApi']);
