<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use Log;
use App\Models\Note;
use App\Domain\Note\Commands\CreateNote;
use App\Domain\Note\Commands\UpdateNote;
use App\Domain\Note\Commands\DeleteNote;

class NoteController extends Controller {
  public function index($id) {
    $user = Auth::guard('api')->user();
    $contact = $user->findContact($id);

    Log::info('Fetch contact notes', ['user' => $user->email, 'contact' => $id]);

    if (! $contact) {
      Log::warning('Cannot find contact', ['user' => $user->email, 'contact' => $id]);

      return response(null, Response::HTTP_FORBIDDEN);
    }

    return response([
      'success' => true,
      'data' => $contact->notes()->get()
    ], Response::HTTP_OK);
  }

  public function store(Request $request) {
    $user = Auth::guard('api')->user();
    $requestData = $request->all();
    $command = CreateNote::from($requestData);
    $logParams = ['params' => $requestData, 'user' => $user->email];

    if (! $command->isValid()) {
      Log::warning('Create note command is invalid', $logParams);

      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $note = $command->execute();

    if ($note) {
      Log::info('Note created', array_merge($logParams, ['note' => $note->id]));

      return response([
        'success' => true,
        'message' => 'Created',
        'data' => $note
      ], Response::HTTP_CREATED);
    } else {
      Log::error('Could not create note', $logParams);
    }

    return response(['success' => false, 'message' => 'Internal Server Error'], Response::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $note = Note::find($id);
    $data = array_merge(['note_id' => $id], $request->only(['title', 'text']));
    $logParams = ['user' => $user->email, 'params' => $data];

    if ($note->contact->user_id == $user->id) {
      $note = UpdateNote::from($data)->execute();

      Log::info($note ? 'Note updated' : 'Could not update note', $logParams);

      return response([
        'success' => (bool) $note,
        'data' => $note,
        'message' => ((bool) $note) ? 'Updated' : 'Not updated'
      ], ((bool) $note) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    Log::error('Cannot update note', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $note = Note::find($id);
    $logParams = ['user' => $user->email, 'note' => $id];

    if (! $note) {
      Log::warning('Note not found', $logParams);

      return response(null, Response::HTTP_NOT_FOUND);
    }

    if ($note->contact->user_id == $user->id) {
      DeleteNote::from(['id' => $note->id])->execute();

      Log::info('Note deleted', $logParams);

      return response(null, Response::HTTP_NO_CONTENT);
    }

    Log::error('Cannot delete note', $logParams);

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
