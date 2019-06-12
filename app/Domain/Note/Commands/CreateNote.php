<?php

namespace App\Domain\Note\Commands;

use Ramsey\Uuid\Uuid;
use App\Domain\Command;
use App\Domain\Note\NoteAggregate;
use App\Models\Note;
use App\Models\Contact;

final class CreateNote extends Command {
  public function __construct(array $bag) {
    $this->attributes['contact_id'] = Uuid::isValid($bag['contact_id']) ?
      $bag['contact_id'] :
      Uuid::fromString($bag['contact_id']);

    $this->attributes['title'] = $bag['title'];
    $this->attributes['text'] = $bag['text'];
  }

  public static function from(array $bag) : CreateNote {
    return new CreateNote($bag);
  }

  public function isValid() : bool {
    return ((bool) Contact::find($this->attributes['contact_id'])) &&
      ! empty($this->attributes['title']) &&
      ! empty($this->attributes['text']);
  }

  public function execute() : ?Note {
    if (! $this->isValid()) return null;

    $newId = Uuid::uuid4();

    NoteAggregate::retrieve($newId)
      ->createNote($this->attributes['contact_id'], $this->attributes['title'], $this->attributes['text']);

    return Note::find($newId);
  }
}
