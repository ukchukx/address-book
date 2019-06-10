<?php

namespace App\Domain\Contact\Events;
use Spatie\EventProjector\ShouldBeStored;

final class ContactCreated implements ShouldBeStored {
  /** @var string */
  public $name;

  /** @var string */
  public $gender;

  /** @var string */
  public $userId;

  public function __construct(string $userId, string $name, string $gender) {
    $this->userId = $userId;
    $this->name = $name;
    $this->gender = $gender;
  }
}
