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

    $numContacts = count($user->contacts);
    Log::info("List $user->email's $numContacts contacts");

    return view('contacts', ['contacts' => json_encode($user->contacts)]);
  }

  public function contact($id) {
    $user = Auth::user();
    $contact = $user->findContact($id);

    if (! $contact) {
      abort(404);
    };

    return view('contact', ['contact' => json_encode($contact)]);
  }
}
