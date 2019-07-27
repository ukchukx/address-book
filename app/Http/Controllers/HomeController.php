<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;
use Auth;

class HomeController extends Controller {
  public function welcome() {
    return view('welcome');
  }

  public function contacts() {
    $user = Auth::user();

    Log::info("List $user->email's contacts");

    return view('contacts', ['contacts' => json_encode($user->contacts())]);
  }
}
