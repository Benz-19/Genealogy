<?php

use App\Http\Controllers\Auth\AuthController;
use CustomRouter\Route;
use App\Http\Controllers\Pages\PagesController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Genealogy\GenealogyController;


require __DIR__ . "/../vendor/autoload.php";

// Landing page
Route::get('/', [PagesController::class, 'landingPage']);
// Login
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login', [AuthController::class, 'showLoginPage']);

// Register
Route::post('/register', [AuthController::class, 'register']);
Route::get('/register', [AuthController::class, 'showRegisterPage']);

// Dashboard
Route::get('/genealogy-dashboard', [UserController::class, 'showDashboard']);

// Genealogy
Route::get('/genealogy', [GenealogyController::class, 'showGenealogyPage']);