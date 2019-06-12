<?php

namespace App\Domain\Note\Commands;

use Ramsey\Uuid\Uuid;
use App\Domain\Command;
use App\Domain\Note\NoteAggregate;
use App\Models\Note;

final class DeleteNote extends Command {
  public function __construct(array $bag) {
    $this->attributes['id'] = Uuid::isValid($bag['id']) ? $bag['id'] : Uuid::fromString($bag['id']);
  }

  public static function from(array $bag) : DeleteNote {
    return new DeleteNote($bag);
  }

  public function isValid() : bool {
    return (bool) Note::find($this->attributes['id']);
  }

  public function execute() {
    if (! $this->isValid()) return;

    NoteAggregate::retrieve($this->attributes['id'])->deleteNote($this->attributes['id']);
  }
}
