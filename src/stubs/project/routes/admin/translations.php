<?php
Route::group([
    'controller' => \App\Http\Controllers\Admin\TranslateController::class,
    'middleware' => ['auth:admin', 'can:view,'.\Waavi\Translation\Models\Translation::class],
    'as' => 'translations.', //naming prefix
    'prefix' => 'translations', //for routes
], function() {
    Route::get('', 'index')->name('index');
    Route::patch('', 'values')->name('values');
    Route::patch('', 'update')->name('update')->middleware('can:update,'.\Waavi\Translation\Models\Translation::class);

    // @HOOK_ROUTES
});
