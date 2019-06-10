<?php

namespace App\Domain\Contact\Events;
use Spatie\EventProjector\ShouldBeStored;

final class ContactNameChanged implements ShouldBeStored {
  /** @var string */
  public $name;

  /** @var string */
  public $contactId;

  public function __construct(string $contactId, string $name) {
    $this->contactId = $contactId;
    $this->name = $name;
  }
}
