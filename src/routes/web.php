<?php
// filepath: /home/satos/coachtech/laravel/demo-case/src/routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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


// ✅ メール認証関連のカスタムルート
Route::middleware(['auth'])->group(function () {
    // メール認証誘導画面
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
    
    // メール認証処理
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'メール認証が完了しました！');
    })->middleware(['signed'])->name('verification.verify');
    
    // 認証メール再送信
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送信しました。');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Route::get('/email/verified', function () {
    //     return view('auth.verified');
    // })->name('email.verified');
    Route::get('/email/verified', function () {
    if (auth()->user()->hasVerifiedEmail()) {
        return redirect()->route('items.index');
    }
    return view('auth.verified');
    })->name('email.verified');
    });

// ✅ 公開ページ（認証不要）
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

// ✅ ログイン + メール認証が必要なページ
Route::middleware(['auth', 'verified'])->group(function () {
    // 出品機能
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
    
    // マイページ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.profile.update');

    // いいね機能
    Route::post('/items/{item}/like', [ItemController::class, 'like'])->name('items.like');
    Route::delete('/items/{item}/unlike', [ItemController::class, 'unlike'])->name('items.unlike');
    
    // 購入機能
    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');
    Route::get('/purchase/konbini/confirm', [PurchaseController::class, 'confirmKonbini'])->name('purchase.konbini.confirm');
    Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    
    // 住所管理
    Route::get('/address/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/address/update', [AddressController::class, 'update'])->name('address.update');

    // コメント機能
    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');
});