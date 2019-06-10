<?php

namespace App\Domain\Contact\Events;
use Spatie\EventProjector\ShouldBeStored;

final class ContactDeleted implements ShouldBeStored {
  /** @var string */
  public $contactId;

  public function __construct(string $contactId) {
    $this->contactId = $contactId;
  }
}
