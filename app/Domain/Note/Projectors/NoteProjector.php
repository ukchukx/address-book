<?php

namespace App\Domain\Note\Projectors;

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
    Note::store(new Note($aggregateUuid, $event->contactId, $event->title, $event->text));
  }

  public function onNoteDeleted(NoteDeleted $event, string $aggregateUuid) {
    Note::remove($aggregateUuid);
  }

  public function onNoteTextChanged(NoteTextChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);
    $note->text = $event->text;
    Note::store($note);
  }

  public function onNoteTitleChanged(NoteTitleChanged $event, string $aggregateUuid) {
    $note = Note::find($aggregateUuid);
    $note->title = $event->title;
    Note::store($note);
  }
}
