<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chat', [ChatController::class, 'index'])->middleware(['auth', 'verified'])->name('chat');

Route::post('/chat/messages/{room_id}', [ChatController::class, 'get_messages'])->middleware(['auth', 'verified'])->name('get_messages');

Route::post('/chat/send/messages/{room_id}', [ChatController::class, 'send_message'])->middleware(['auth', 'verified'])->name('send_message');

Route::get('/profile', [ProfileController::class, 'index'])->middleware(['auth', 'verified'])->name('profile');

Route::post('/profile/update', [ProfileController::class, 'update'])->middleware(['auth'])->name('profile.update');

Route::get('/members', [MemberController::class, 'index'])->middleware(['auth'])->name('members');

Route::post('/add/friend', [MemberController::class, 'add_friend'])->middleware(['auth'])->name('add_friend');

require __DIR__.'/auth.php';
