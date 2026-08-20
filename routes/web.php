<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, config('translatable.locales'), true), 404);

    session(['admin_locale' => $locale]);

    return redirect()->back();
})->middleware('auth:admin')->name('admin.locale');
