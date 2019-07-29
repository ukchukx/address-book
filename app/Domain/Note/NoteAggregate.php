<?php

namespace App\Domain\Note;

use Log;
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
    Log::info('Command received', [
      'command' => 'createNote',
      'params' => ['contactId' => $contactId, 'title' => $title, 'text' => $text]
    ]);

    $this->recordThat(new NoteCreated($contactId, $title, $text));

    $this->persist();
  }

  public function updateNote(string $noteId, string $title, string $text) {
    $updated = false;

    Log::info('Command received', [
      'command' => 'updateNote',
      'params' => ['noteId' => $noteId, 'title' => $title, 'text' => $text]
    ]);

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
    Log::info('Command received', ['command' => 'deleteNote', 'params' => ['id' => $id]]);

    $this->recordThat(new NoteDeleted($id));

    $this->persist();
  }

  public function applyNoteCreated(NoteCreated $event) {
    Log::info('Applying event', ['event' => 'NoteCreated', 'event' => $event]);

    $this->title = $event->title;
    $this->text = $event->text;
    $this->contactId = $event->contactId;

    $this->persist();
  }

  public function applyNoteTitleChanged(NoteTitleChanged $event) {
    Log::info('Applying event', ['event' => 'NoteTitleChanged', 'event' => $event]);

    $this->title = $event->title;

    $this->persist();
  }

  public function applyNoteTextChanged(NoteTextChanged $event) {
    Log::info('Applying event', ['event' => 'NoteTextChanged', 'event' => $event]);

    $this->text = $event->text;

    $this->persist();
  }
}
