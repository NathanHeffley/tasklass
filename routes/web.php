<?php

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

Route::get('/login', 'Auth\LoginController@show');
Route::post('/login', 'Auth\LoginController@login');
Route::post('/logout', 'Auth\LoginController@logout');

Route::get('/register', 'Auth\RegisterController@show');
Route::post('/register', 'Auth\RegisterController@register');

Route::get('/todos', 'TodosController@index');
Route::get('/todos.json', 'TodosController@indexJson');
Route::post('/todos', 'TodosController@store');
Route::put('/todos/{id}', 'TodosController@update');
Route::delete('/todos/{id}', 'TodosController@archive');
