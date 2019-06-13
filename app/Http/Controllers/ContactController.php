<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Models\Contact;
use App\Domain\Contact\Commands\CreateContact;
use App\Domain\Contact\Commands\DeleteContact;
use App\Domain\Contact\Commands\UpdateContact;

class ContactController extends Controller {
  public function store(Request $request) {
    $data = $request->all();
    $data['user_id'] = Auth::guard('api')->user()->id;
    $command = CreateContact::from($data);

    if (! $command->isValid()) {
      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $contact = $command->execute();

    return response([
      'success' => true,
      'message' => 'Created',
      'data' => $contact
    ], Response::HTTP_CREATED);
  }

  public function update(Request $request, $id) {
    $contact = Auth::guard('api')->user()->findContact($id);

    if ($contact) {
      $contact = UpdateContact::from(array_merge($request->all(), ['contact_id' => $id]))->execute();

      return response([
        'success' => (bool) $contact,
        'data' => $contact,
        'message' => ((bool) $contact) ? 'Updated' : 'Not updated'
      ], ((bool) $contact) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $contact = Auth::guard('api')->user()->findContact($id);

    if ($contact) {
      DeleteContact::from(['id' => $contact->id])->execute();

      return response(null, Response::HTTP_NO_CONTENT);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
