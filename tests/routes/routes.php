<?php

use Illuminate\Support\Facades\Route;
use JPNut\Tests\Controllers\BarController;
use JPNut\Tests\Controllers\FooController;

Route::get('/foo', FooController::class.'@index')->name('foo.index');
Route::get('/foo/{foo}', FooController::class.'@fetch')->name('foo.fetch');
Route::put('/foo/{foo}', FooController::class.'@update')->name('foo.update');
Route::delete('/foo/{foo}', FooController::class.'@delete')->name('foo.delete');
Route::post('/foo', FooController::class.'@create')->name('foo.create');

Route::get('/bar/scalar-return-type', BarController::class.'@scalarReturnType')
    ->name('bar.scalar');
Route::get('/bar/optional-scalar-return-type', BarController::class.'@optionalScalarReturnType')
    ->name('bar.optional-scalar');
Route::get('/bar/{?id}', BarController::class.'@optionalParameter')
    ->name('bar.optional-parameter');
Route::get('/bar/query-params', BarController::class.'@queryParams')
    ->name('bar.query-params');
Route::get('/bar/http-only', ['http', 'uses' => BarController::class.'@httpOnly'])
    ->name('bar.http-only');
Route::get('/bar/https-only', ['https', 'uses' => BarController::class.'@httpsOnly'])
    ->name('bar.https-only');
Route::get('/bar/domain', BarController::class.'@domain')
    ->domain('https://localhost.dev')
    ->name('bar.domain');

