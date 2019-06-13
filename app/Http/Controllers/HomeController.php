<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {
  public function welcome() {
    return view('welcome');
  }

  public function contacts() {
    return view('contacts', ['contacts' => json_encode(\Auth::user()->contacts())]);
  }
}