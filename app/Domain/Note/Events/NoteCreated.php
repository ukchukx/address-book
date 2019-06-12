<?php

namespace App\Domain\Note\Events;
use Spatie\EventProjector\ShouldBeStored;

final class NoteCreated implements ShouldBeStored {
  /** @var string */
  public $title;

  /** @var string */
  public $text;

  /** @var string */
  public $contactId;

  public function __construct(string $contactId, string $title, string $text) {
    $this->contactId = $contactId;
    $this->title = $title;
    $this->text = $text;
  }
}
