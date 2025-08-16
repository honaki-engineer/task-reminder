<?php

use App\Http\Controllers\GuestLoginController;
use App\Http\Controllers\ProfileController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/guest-login', [GuestLoginController::class, 'login'])->name('guest.login');

Route::middleware('auth')->group(function () {
    Route::get('/tasks/one-day', [TaskController::class, 'oneDay'])->name('tasks.one_day');
    Route::post('/tasks/{task}', [TaskController::class, 'complete'])->name('tasks.complete');
    Route::resource('tasks', TaskController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
