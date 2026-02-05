<?php

use CustomRouter\Route;
use App\Http\Controllers\Genealogy\GenealogyController;


// Genealogy
Route::get('/genealogy', [GenealogyController::class, 'showGenealogyPage']);

// Data Endpoint Routes
Route::get('/api/genealogy/tree', [GenealogyController::class, 'getTreeData']);
Route::get('/api/genealogy/stats', [GenealogyController::class, 'getStats']);
Route::get('/api/genealogy/upline', [GenealogyController::class, 'getUplineData']);