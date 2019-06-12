<?php

namespace App\Domain\Note\Events;
use Spatie\EventProjector\ShouldBeStored;

final class NoteDeleted implements ShouldBeStored {
  /** @var string */
  public $noteId;

  public function __construct(string $noteId) {
    $this->noteId = $noteId;
  }
}
