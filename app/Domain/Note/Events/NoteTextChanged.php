<?php

namespace App\Domain\Note\Events;
use Spatie\EventProjector\ShouldBeStored;

final class NoteTextChanged implements ShouldBeStored {
  /** @var string */
  public $text;

  /** @var string */
  public $noteId;

  public function __construct(string $noteId, string $text) {
    $this->noteId = $noteId;
    $this->text = $text;
  }
}
