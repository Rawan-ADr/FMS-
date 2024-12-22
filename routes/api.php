<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\UserController;
 use App\Http\Controllers\GroupController;
   use App\Http\Controllers\FileController;
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

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);

Route::group(['middleware' => ['auth:sanctum']], function() {
    Route::get('logout', [UserController::class, 'logout']);
    
    Route::prefix('group')->group(function () {
        Route::post('create',[GroupController::class,'create'])
        ->middleware('can:group.create');
        Route::post('update/{group_id}',[GroupController::class,'update'])
        ->middleware('can:group.update');
        Route::get('delete/{group_id}',[GroupController::class,'delete'])
        ->middleware('can:group.delete');
        Route::get('index',[GroupController::class,'index'])
        ->middleware('can:group.index');

    });

    Route::prefix('user')->group(function () {
        Route::get('index', [UserController::class, 'index'])
        ->middleware('can:user.index');
        Route::post('add/to/group', [UserController::class, 'addUserToGroup'])
        ->middleware('can:userToGroup.add');
    });
          Route::prefix('file')->group(function () {
        Route::post('addFile', [FileController::class, 'addFile'])
        ->middleware('can:file.add');
         Route::get('/reserveFile/{id}', [FileController::class, 'reserveFile'])
        ->middleware('UserLogg','FileLogg');
         Route::get('/unreserveFile/{id}', [FileController::class, 'unreserveFile'])
        ->middleware('UserLogg','FileLogg');
         Route::post('/reserveAll/{ids}', [FileController::class, 'reserveAll'])
        ->middleware('UserLogg','FileLogg');
         Route::post('/upDateFile/{id}', [FileController::class, 'upDateFile'])
        ->middleware('UserLogg','FileLogg');
        Route::get('/getFile/{id}', [FileController::class, 'getFile']);
        Route::get('/showFileLogs/{id}', [FileController::class, 'showFileLogs']);
        Route::get('/showUserLogs/{id}', [FileController::class, 'showUserLogs']);
        Route::get('/showFileCopy/{id}', [FileController::class, 'showFileCopy']);
    });

    
});