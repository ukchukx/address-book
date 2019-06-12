<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api']], function() {
  Route::resource('contacts', 'ContactController', ['only' => ['store', 'update', 'destroy']]);

  Route::get('contacts/{id}/notes', 'NoteController@index');
  Route::post('notes', 'NoteController@store');
  Route::put('notes/{id}', 'NoteController@update');
  Route::delete('notes/{id}', 'NoteController@destroy');
});
