<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
 use App\Http\Controllers\UserController;
 use App\Http\Controllers\GroupController;
 use App\Http\Controllers\FileController;
 use App\Http\Controllers\ReportController;
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
        Route::get('index/{group_id}', [UserController::class, 'index'])
        ->middleware('can:user.index');
        Route::post('add/to/group', [UserController::class, 'addUserToGroup'])
        ->middleware('can:userToGroup.add');
        Route::post('search/by/name/{group_id}', [UserController::class, 'searchForUserByName']);
    });
          Route::prefix('file')->group(function () {
        Route::post('addFile', [FileController::class, 'addFile'])
        ->middleware('can:file.add','UserLogg','FileLogg');
         Route::get('/reserveFile/{id}', [FileController::class, 'reserveFile'])
        ->middleware('UserLogg','FileLogg');
         Route::get('/unreserveFile/{id}', [FileController::class, 'unreserveFile'])
        ->middleware('UserLogg','FileLogg');
         Route::post('reserveAll/{ids}', [FileController::class, 'reserveAll'])->where('ids','.*')
        ->middleware('UserLogg','FileLogg');
         Route::post('/upDateFile/{id}', [FileController::class, 'upDateFile'])
        ->middleware('UserLogg','FileLogg');
        Route::get('/getFile/{id}', [FileController::class, 'getFile']);
        Route::get('/showFileLogs/{id}', [FileController::class, 'showFileLogs']);
        Route::get('/showUserLogs/{id}', [FileController::class, 'showUserLogs']);
        Route::get('/showFileCopy/{id}', [FileController::class, 'showFileCopy']);
        Route::get('/getFileCopies/{id}', [FileController::class, 'getFileCopies']);
        Route::get('/showFileContent/{id}', [FileController::class, 'showFileContent']);
        Route::get('/getGroupFile/{id}', [FileController::class, 'getGroupFile']);
        Route::get('/approveFile/{id}', [FileController::class, 'approveFile']);
        Route::get('/getadminFile/{id}', [FileController::class, 'getadminFile']);
        Route::get('/rejectFile/{id}', [FileController::class, 'rejectFile']);
        Route::get('/getNotifications/{id}', [FileController::class, 'getNotifications']);
        Route::post('showFileEdit', [FileController::class, 'showFileEdit']);

    });

    Route::prefix('report')->group(function () {
        Route::get('user/report/index/{group_id}', [ReportController::class, 'userReportindex'])
        ->middleware('can:userReport.index');
        Route::get('file/report/index/{group_id}', [ReportController::class, 'fileReportindex'])
        ->middleware('can:fileReport.index');

        Route::get('user/index/{group_id}/{user_id}', [ReportController::class, 'ReportindexForUser'])
        ->middleware('can:userReport.index');
        Route::get('file/index/{group_id}/{file_id}', [ReportController::class, 'ReportindexForFile'])
        ->middleware('can:fileReport.index');

        Route::get('user-report/pdf/{id}', [ReportController::class, 'exportUserReportToPdf']);
        Route::get('user-report/csv/{id}', [ReportController::class, 'exportUserReportToCsv']);

        Route::get('file-report/pdf/{id}', [ReportController::class, 'exportFileReportToPdf']);
        Route::get('file-report/csv/{id}', [ReportController::class, 'exportFileReportToCsv']);
       
    });

   

    
});