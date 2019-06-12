<?php

namespace App\Domain\Note;

use Spatie\EventProjector\AggregateRoot;
use App\Domain\Note\Events\NoteCreated;
use App\Domain\Note\Events\NoteDeleted;
use App\Domain\Note\Events\NoteTextChanged;
use App\Domain\Note\Events\NoteTitleChanged;

final class NoteAggregate extends AggregateRoot {
  /** @var string */
  public $title;

  /** @var string */
  public $text;

  /** @var string */
  public $contactId;

  public function createNote(string $contactId, string $title, string $text) {
    $this->recordThat(new NoteCreated($contactId, $title, $text));

    $this->persist();
  }

  public function updateNote(string $noteId, string $title, string $text) {
    $updated = false;

    if (! empty($title) && $title != $this->title) {
      $this->recordThat(new NoteTitleChanged($noteId, $title));

      $updated = true;
    }

    if (! empty($text) && $text != $this->text) {
      $this->recordThat(new NoteTextChanged($noteId, $text));

      $updated = true;
    }

    if ($updated) $this->persist();
  }

  public function deleteNote(string $id) {
    $this->recordThat(new NoteDeleted($id));

    $this->persist();
  }

  public function applyNoteCreated(NoteCreated $event) {
    $this->title = $event->title;
    $this->text = $event->text;
    $this->contactId = $event->contactId;

    $this->persist();
  }

  public function applyNoteTitleChanged(NoteTitleChanged $event) {
    $this->title = $event->title;

    $this->persist();
  }

  public function applyNoteTextChanged(NoteTextChanged $event) {
    $this->text = $event->text;

    $this->persist();
  }
}
