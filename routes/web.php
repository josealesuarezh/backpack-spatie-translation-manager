<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});
Route::group([
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    )
], function () {
    Route::get('/import',[\App\Http\Controllers\Admin\LanguageCrudController::class,'import']);
    Route::get('/translation/{id}/{lang}',[\App\Http\Controllers\Admin\LanguageTranslationCrudController::class,'getTranslation']);
});

