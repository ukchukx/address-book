<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller {
  public function welcome() {
    return view('welcome');
  }

  public function contacts() {
    return view('contacts', ['contacts' => \Auth::user()->contacts()->get()]);
  }
}
