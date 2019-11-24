<?php

namespace App\Domain\Note\Events;
use Spatie\EventSourcing\ShouldBeStored;

final class NoteTitleChanged implements ShouldBeStored {
  /** @var string */
  public $title;

  /** @var string */
  public $noteId;

  public function __construct(string $noteId, string $title) {
    $this->noteId = $noteId;
    $this->title = $title;
  }
}
