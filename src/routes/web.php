<?php

use App\Http\Controllers\GuestLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SlackController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ----- デフォルト使用しない
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// ----- トップページへ
Route::get('/', function () { return view('welcome'); })->name('welcome');

// ----- エラー画面の確認テスト
// 400 Bad Request
Route::get('/force-400', function () { abort(400, 'Bad Request Test'); });
// 401 Unauthorized
Route::get('/force-401', function () { abort(401, 'Unauthorized Test'); });
// 403 Forbidden
Route::get('/force-403', function () { abort(403, 'Forbidden Test'); });
// 404 Not Found
Route::get('/force-404', function () { abort(404, 'Not Found Test'); });
// 419 Page Expired
Route::get('/force-419', function () { abort(419, 'Page Expired Test'); });
// 422 Unprocessable Entity
Route::get('/force-422', function () { abort(422, 'Unprocessable Entity Test'); });
// 429 Too Many Requests
Route::get('/force-429', function () { abort(429, 'Too Many Requests Test'); });
// 500 Internal Server Error（throwで例外を発生させる）
Route::get('/force-500', function () { throw new \Exception('Internal Server Error Test'); });
// 503 Service Unavailable
Route::get('/force-503', function () { abort(503, 'Service Unavailable Test'); });

// ----- ゲストログイン
Route::get('/guest-login', [GuestLoginController::class, 'login'])->name('guest.login');

// ----- ログイン中のみ
Route::middleware('auth')->group(function () {
    Route::get('/tasks/one-day', [TaskController::class, 'oneDay'])->name('tasks.one_day');
    Route::post('/tasks/{task}', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::resource('tasks', TaskController::class);
    
    Route::get('/slacks', [SlackController::class, 'index'])->name('slacks.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
