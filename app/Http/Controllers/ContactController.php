<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Log;
use App\Models\Contact;
use App\Domain\Contact\Commands\CreateContact;
use App\Domain\Contact\Commands\DeleteContact;
use App\Domain\Contact\Commands\UpdateContact;
use App\Domain\Note\Commands\CreateNote;

class ContactController extends Controller {
  public function store(Request $request) {
    $user = Auth::guard('api')->user();
    $data = $request->all();
    $data['user_id'] = $user->id;
    $command = CreateContact::from($data);
    $logParams = ['user' => $user->email, 'params' => $data];

    if (! $command->isValid()) {
      Log::warning('Command to create contact is invalid', $logParams);

      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $contact = $command->execute();

    if ($contact) {
      $logParams = array_merge($logParams, ['contact' => $contact->id]);

      Log::info('Contact created', $logParams);

      $noteParams = [
        'contact_id' => $contact->id, 
        'title' => 'General notes', 
        'text' => 'New text...'
      ];
      $command = CreateNote::from($noteParams);
      $note = $command->execute();
      $logParams = array_merge($logParams, ['note_params' => $noteParams]);

      if ($note) {
        Log::info('Default note created for new contact', array_merge($logParams, ['note' => $note->id]));
      } else {
        Log::error('Could not create default note for new contact', $logParams);
      }

      return response([
        'success' => true,
        'message' => 'Created',
        'data' => $contact
      ], Response::HTTP_CREATED);
    } else {
      Log::error('Could not create contact', $logParams);
    }

    return response(['success' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $contact = $user->findContact($id);
    $data = array_merge($request->all(), ['contact_id' => $id]);
    $logParams = ['user' => $user->email, 'params' => $data];

    if ($contact) {
      $contact = UpdateContact::from($data)->execute();

      Log::info($contact ? 'Contact updated' : 'Could not update contact', $logParams);

      return response([
        'success' => (bool) $contact,
        'data' => $contact,
        'message' => ((bool) $contact) ? 'Updated' : 'Not updated'
      ], ((bool) $contact) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    Log::error('Cannot update contact', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $contact = $user->findContact($id);
    $logParams = ['user' => $user->email, 'contact' => $id];

    if (! $contact) {
      Log::warning('Contact not found', $logParams);

      return response(null, Response::HTTP_NOT_FOUND);
    }

    if ($contact) {
      DeleteContact::from(['id' => $contact->id])->execute();

      Log::info('Contact deleted', $logParams);

      return response(null, Response::HTTP_NO_CONTENT);
    }

    Log::error('Cannot delete contact', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
