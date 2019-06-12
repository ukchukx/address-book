<?php

namespace App\Domain\Note\Projectors;

use Ramsey\Uuid\Uuid;
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
    Note::create([
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'text' => $event->text,
      'title' => $event->title
    ]);
  }

  public function onNoteDeleted(NoteDeleted $event, string $aggregateUuid) {
    Note::where('id', $aggregateUuid)->delete();
  }

  public function onNoteTextChanged(NoteTextChanged $event, string $aggregateUuid) {
    Note::where('id', $aggregateUuid)->update(['text' => $event->text]);
  }

  public function onNoteTitleChanged(NoteTitleChanged $event, string $aggregateUuid) {
    Note::where('id', $aggregateUuid)->update(['title' => $event->title]);
  }
}
