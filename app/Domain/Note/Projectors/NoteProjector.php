<?php

namespace App\Domain\Note\Projectors;

use Log;
use Spatie\EventProjector\Projectors\Projector;
use Spatie\EventProjector\Projectors\ProjectsEvents;
use App\Domain\Note\Events\NoteCreated;
use App\Domain\Note\Events\NoteDeleted;
use App\Domain\Note\Events\NoteTextChanged;
use App\Domain\Note\Events\NoteTitleChanged;
use App\Models\Note;

class NoteProjector implements Projector {
  use ProjectsEvents;

  public function onNoteCreated(NoteCreated $event, string $aggregateUuid) {
    $params = [
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'title' => $event->title,
      'text' => $event->text
    ];
    $logParams = ['params' => $params, 'event' => 'onNoteCreated'];

    if (Note::create($params)) {
      Log::info('Created note', $logParams);
    } else {
      Log::info('Failed to create note', $logParams);
    }
  }

  public function onNoteDeleted(NoteDeleted $event, string $aggregateUuid) {
    $logParams = ['id' => $aggregateUuid, 'event' => 'onNoteDeleted'];

    if (Note::where('id', $aggregateUuid)->delete()) {
      Log::info('Deleted note', $logParams);
    } else {
      Log::info('Could not delete note', $logParams);
    }
  }

  public function onNoteTextChanged(NoteTextChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'text' => $event->text, 'event' => 'onNoteTextChanged'];

    if (! $note) {
      Log::info('Note not found', $logParams);
      return;
    }

    $note->text = $event->text;

    if ($note->save()) {
      Log::info('Note text updated', $logParams);
    } else {
      Log::info('Could not update note text', $logParams);
    }
  }

  public function onNoteTitleChanged(NoteTitleChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'title' => $event->title, 'event' => 'onNoteTitleChanged'];

    if (! $note) {
      Log::info('Note not found', $logParams);
      return;
    }

    $note->title = $event->title;
    if ($note->save()) {
      Log::info('Note title updated', $logParams);
    } else {
      Log::info('Could not update note title', $logParams);
    }
  }
}
