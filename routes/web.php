<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;

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
//     return view('welcome');
// });

// Route::get('/','HomeController@home')->name('home');

Route::group(['middleware'=>['revalidate_back_history']], function(){

    Route::get('/', [HomeController::class, 'home'])->name('home');

    Route::group(['prefix'=>'auth','middleware'=>['custom_guest']],function(){
        Route::get('/registration', [AuthController::class, 'getRegister'])->name('getRegister');
        Route::post('/registration', [AuthController::class,'postRegister'])->name('postRegister');
        Route::post('/check_email_unique', [AuthController::class, 'check_email_unique'])->name('check_email_unique');
        Route::get('/verify-email/{verification_code}', [AuthController::class, 'verify_email'])->name('verify_email');
        Route::get('/login', [AuthController::class, 'getlogin'])->name('getlogin');
        Route::post('/login', [AuthController::class, 'postlogin'])->name('postlogin');
    });
    
    Route::get('/auth/logout',[AuthController::class,'logout'])->name('logout')->middleware('custom_auth');
    
    Route::prefix('profile')->middleware(['custom_auth'])->group(function () {
        Route::get('/dashboard', [ProfileController::class, 'dashboard'])->name('dashboard');
        Route::get('/edit-profile', [ProfileController::class, 'edit_profile'])->name('edit_profile');
        Route::put('/edit-profile', [ProfileController::class, 'update_profile'])->name('update_profile');
        Route::get('/change-password', [ProfileController::class, 'change_password'])->name('change_password');
        Route::post('/update-password', [ProfileController::class, 'update_password'])->name('update_password');
    });
    
});

