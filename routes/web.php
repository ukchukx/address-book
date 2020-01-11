<?php

Auth::routes();

Route::get('/', 'HomeController@welcome')->name('welcome');

Route::group(['middleware' => ['auth']], function() {
  Route::get('/logout', 'Auth\LoginController@logout');
  Route::get('/contacts', 'HomeController@contacts')->name('contacts');
  Route::get('/contacts/{id}', 'HomeController@contact')->name('contact');
});
