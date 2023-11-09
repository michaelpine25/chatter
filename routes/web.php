<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ImageController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Events\Hello;

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

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return Inertia::render('Welcome'); 
})->name('welcome');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/conversation/{id}', function() {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('conversation');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/profile-picture', [ProfileController::class, 'updateProfilePicture'])->name('picture.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/profile-picture/ProfilePictures/{id}', [ImageController::class, 'showProfilePicture']);
Route::get('/conversation-photos/ConversationPhotos/{id}', [ImageController::class, 'showConversationPhoto']);
Route::get('/picture-messages/PictureMessages/{id}', [ImageController::class, 'showPictureMessage']);

// "API" routes bound by web auth
Route::get('/api/conversations', [ConversationController::class, 'list']);
Route::post('/api/conversations', [ConversationController::class, 'store'])->name('conversation.store');
Route::post('/api/conversations/{id}/photo', [ConversationController::class, 'updateConversationPhoto']);
Route::post('/api/conversation/{id}/message', [MessageController::class, 'store'])->name('message.store');
Route::get('/api/conversation/{id}/authorize', [ConversationController::class, 'authorizeUser']);

Route::post('/api/invitations/{id}', [InvitationController::class, 'store']);
Route::post('/api/invitation/accept', [InvitationController::class, 'accept']);
Route::delete('/api/invitation/deny/{id}', [InvitationController::class, 'deny']);
Route::get('/api/invitations', [InvitationController::class, 'list']);

Route::get('/api/participants/{id}', [ParticipantController::class, 'list']);
Route::post('/api/participants/{id}/unreadCount', [ParticipantController::class, 'resetUnreadCount']);

Route::get('/api/conversation/{id}/messages', [MessageController::class, 'list'])->name('message.list');

Route::post('/api/participants/{id}/delete', [ParticipantController::class, 'destroy']);

Route::get('/api/user', function () {
    return Auth::user();
});

require __DIR__.'/auth.php';
