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

// Authentication Routes
Route::get('login', 'Auth\LoginController@index')->name('login');
Route::get('login/captcha', 'Auth\LoginController@update')->name('login.captcha');
Route::get('login/{provider?}', 'Auth\LoginController@show')->name('login.show');
Route::match(['get', 'post'], 'login/{provider}/process', 'Auth\LoginController@store')->name('login.process');
Route::match(['get', 'post'], 'logout/{redirect?}', 'Auth\LoginController@destroy')->name('logout');

// Basic Registration
Route::get('register/{provider?}', 'Auth\RegisterController@show')->name('register.show');
Route::match(['get', 'post'], 'register/{provider?}/process', 'Auth\RegisterController@store')->name('register.process');

// Vues temporaires, uniquement de l'affichage de liens
// Route::get('/home', 'HomeController@welcome')->name('welcome');

// Route principale et Autres vers React
Route::get('/', 'HomeController@react')->name('react');
// Route::any('{whatever}', 'HomeController@react')->where('whatever', '.*');
Route::any('{whatever}', 'HomeController@react')->where('whatever', '^((?!\/oauth).)*');

