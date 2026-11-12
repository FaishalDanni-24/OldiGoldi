<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/explore', function () {
    return view('explore');
});

// Payment gateway return pages (server-side handoff from sandbox)
Route::get('/payment/return', function () {
    return response('Payment return received. You may close this window.', 200);
})->name('payment.return');

Route::get('/payment/finish', function () {
    return response('Payment finished. You may close this window.', 200);
})->name('payment.finish');

Route::get('/payment/cancel', function () {
    return response('Payment cancelled.', 200);
})->name('payment.cancel');
