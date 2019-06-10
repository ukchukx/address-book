<?php

use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:api']], function() {
  Route::resource('contacts', 'ContactController', ['only' => ['store', 'update', 'destroy']]);
});
