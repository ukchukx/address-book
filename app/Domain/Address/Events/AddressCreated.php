<?php

namespace App\Domain\Address\Events;
use Spatie\EventProjector\ShouldBeStored;

final class AddressCreated implements ShouldBeStored {
  /** @var string */
  public $key;

  /** @var string */
  public $value;

  /** @var string */
  public $contactId;

  public function __construct(string $contactId, string $key, string $value) {
    $this->contactId = $contactId;
    $this->key = $key;
    $this->value = $value;
  }
}
