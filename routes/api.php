<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LombaController;
use App\Http\Controllers\PendaftarController;
use App\Http\Controllers\SettingController;

Route::get('/lombas', [LombaController::class, 'index']);
Route::post('/lombas', [LombaController::class, 'store']);
Route::put('/lombas/{id}', [LombaController::class, 'update']);
Route::delete('/lombas/{id}', [LombaController::class, 'destroy']);
Route::post('/lombas/{id}/juara', [LombaController::class, 'updateJuara']);
Route::post('/lombas/ai-generate', [LombaController::class, 'generateAiDescription']);

Route::get('/pendaftars', [PendaftarController::class, 'index']);
Route::post('/pendaftars', [PendaftarController::class, 'store']);
Route::put('/pendaftars/{id}', [PendaftarController::class, 'update']);
Route::delete('/pendaftars/{id}', [PendaftarController::class, 'destroy']);
Route::post('/pendaftars/scan', [PendaftarController::class, 'scanCheckin']);

Route::get('/settings', [SettingController::class, 'getSettings']);
Route::post('/settings', [SettingController::class, 'saveSettings']);
Route::post('/upload', [SettingController::class, 'uploadFile']);