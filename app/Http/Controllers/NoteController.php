<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Auth;
use App\Models\Note;
use App\Domain\Note\Commands\CreateNote;
use App\Domain\Note\Commands\UpdateNote;
use App\Domain\Note\Commands\DeleteNote;

class NoteController extends Controller {
  public function index($id) {
    $contact = Auth::guard('api')->user()->contacts()->where('id', $id)->first();

    if (! $contact) return response(null, Response::HTTP_FORBIDDEN);

    return response([
      'success' => true,
      'data' => $contact->notes()->get()->jsonSerialize()
    ], Response::HTTP_OK);
  }

  public function store(Request $request) {
    $command = CreateNote::from($request->all());

    if (! $command->isValid()) {
      return response([
        'success' => false,
        'message' => 'Invalid params'
      ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    $note = $command->execute();

    return response([
      'success' => true,
      'message' => 'Created',
      'data' => $note->jsonSerialize()
    ], Response::HTTP_CREATED);
  }

  public function update(Request $request, $id) {
    $user = Auth::guard('api')->user();
    $note = Note::findOrFail($id);

    if ($note->contact->user->id == $user->id) {
      $note = UpdateNote::from(array_merge(['note_id' => $id], $request->only(['title', 'text'])))->execute();

      return response([
        'success' => (bool) $note,
        'data' => $note,
        'message' => ((bool) $note) ? 'Updated' : 'Not updated'
      ], ((bool) $note) ? Response::HTTP_OK : Response::HTTP_BAD_REQUEST);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }

  public function destroy($id) {
    $user = Auth::guard('api')->user();
    $note = Note::findOrFail($id);

    if ($note->contact->user->id == $user->id) {
      DeleteNote::from(['id' => $note->id])->execute();

      return response(null, Response::HTTP_NO_CONTENT);
    }

    return response(null, Response::HTTP_FORBIDDEN);
  }
}
