<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| ここにWebルートを定義します。
*/

Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/')->with('success', 'メール認証が完了しました！');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送信しました。');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::get('/email/verified', function () {
        if (auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('items.index');
        }
        return view('auth.verified');
    })->name('email.verified');
});

Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{id}', [ItemController::class, 'show'])->name('items.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/profile', [MypageController::class, 'editProfile'])->name('mypage.profile.edit');
    Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.profile.update');

    Route::post('/items/{item}/like', [ItemController::class, 'like'])->name('items.like');
    Route::delete('/items/{item}/unlike', [ItemController::class, 'unlike'])->name('items.unlike');

    Route::get('/purchase/{item}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('/purchase/{item}/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');
    Route::get('/purchase/konbini/confirm', [PurchaseController::class, 'confirmKonbini'])->name('purchase.konbini.confirm');
    Route::get('/purchase/{item}/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');

    Route::get('/address/edit', [AddressController::class, 'edit'])->name('address.edit');
    Route::post('/address/update', [AddressController::class, 'update'])->name('address.update');

    Route::post('/items/{item}/comments', [CommentController::class, 'store'])->name('comments.store');

    Route::get('/chat/{purchaseId}', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');
    Route::get('/chat/{purchaseId}/edit/{messageId}', [ChatController::class, 'edit'])->name('chat.edit');
    Route::put('/chat/{purchaseId}/update/{messageId}', [ChatController::class, 'update'])->name('chat.update');
    Route::delete('/chat/{purchaseId}/delete/{messageId}', [ChatController::class, 'destroy'])->name('chat.destroy');
    Route::post('/chat/{purchaseId}/complete', [ChatController::class, 'completeTransaction'])->name('chat.completeTransaction');
});