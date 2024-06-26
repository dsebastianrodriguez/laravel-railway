<?php

use App\Http\Controllers\Api\MstUsuariosController;
use App\Http\Controllers\Api\studentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



// Route::get('/student',function(){
//     return 'STUDENT LIST';
// });
Route::get('/student/{id}', [studentController::class, 'show']);
// Route::get('/student/{id}',function(){
//     return 'STUDENT ONE';
// });
Route::post('/student', [studentController::class, 'store']);
// Route::post('/student',function(){
//     return 'CREANDO ESTUDIANTES';
// });
Route::put('/student/{id}', [studentController::class, 'update']);
// Route::put('/student/{id}',function(){
//     return 'ACTUALIZANDO ESTUDIANTES';
// });
Route::patch('/student/{id}', [studentController::class, 'updatePartial']);
Route::delete('/student/{id}', [studentController::class, 'destroy']);
// Route::delete('/student/{id}',function(){
//     return 'ELIMINANDO ESTUDIANTES';
// });

Route::post('/auth', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

//Rutas protegidas por api key
Route::middleware('x_api_key')->group(function () {
    Route::get('/student', [studentController::class, 'index']);

    Route::middleware('jwt.verify')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/profile/{login}', [MstUsuariosController::class, 'show']);
        Route::get('/usuarios', [MstUsuariosController::class, 'index']);
    });
});

//Rutas protegidas por jwt
