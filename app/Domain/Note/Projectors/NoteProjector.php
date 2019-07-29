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
    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    Note::create([
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'title' => $event->title,
      'text' => $event->text
    ]);
  }

  public function onNoteDeleted(NoteDeleted $event, string $aggregateUuid) {
    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    Note::where('id', $aggregateUuid)->delete();
  }

  public function onNoteTextChanged(NoteTextChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $note->text = $event->text;
    $note->save();
  }

  public function onNoteTitleChanged(NoteTitleChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $note->title = $event->title;
    $note->save();
  }
}
