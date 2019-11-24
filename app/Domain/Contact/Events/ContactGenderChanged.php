<?php

namespace App\Domain\Contact\Events;
use Spatie\EventSourcing\ShouldBeStored;

final class ContactGenderChanged implements ShouldBeStored {
  /** @var string */
  public $gender;

  /** @var string */
  public $contactId;

  public function __construct(string $contactId, string $gender) {
    $this->contactId = $contactId;
    $this->gender = $gender;
  }
}
