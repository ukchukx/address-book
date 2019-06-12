<?php

namespace App\Domain\Note\Commands;

use Ramsey\Uuid\Uuid;
use App\Models\Note;
use App\Domain\Command;
use App\Domain\Note\NoteAggregate;

final class UpdateNote extends Command {
  public function __construct(array $bag) {
    $this->attributes['note_id'] = Uuid::isValid($bag['note_id']) ?
      $bag['note_id'] :
      Uuid::fromString($bag['note_id']);

    if (! empty($bag['title'])) $this->attributes['title'] = $bag['title'];
    if (! empty($bag['text'])) $this->attributes['text'] = $bag['text'];
  }

  public static function from(array $bag) : UpdateNote {
    return new UpdateNote($bag);
  }

  public function isValid() : bool {
    $noteExists = ((bool) Note::find($this->attributes['note_id']));
    // Ignore if not provided
    $titleOk = ! empty($this->attributes['title']) || true;
    $textOk = ! empty($this->attributes['text']) || true;

    return $noteExists && $titleOk && $textOk;
  }

  public function execute() : ?Note {
    if (! $this->isValid()) return null;

    NoteAggregate::retrieve($this->attributes['note_id'])
      ->updateNote(
        $this->attributes['note_id'],
        ! empty($this->attributes['title']) ? $this->attributes['title'] : '',
        ! empty($this->attributes['text']) ? $this->attributes['text'] : ''
      );

    return Note::find($this->attributes['note_id']);
  }
}
