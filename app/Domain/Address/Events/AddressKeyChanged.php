<?php

namespace App\Domain\Address\Events;
use Spatie\EventProjector\ShouldBeStored;

final class AddressKeyChanged implements ShouldBeStored {
  /** @var string */
  public $key;

  /** @var string */
  public $addressId;

  public function __construct(string $addressId, string $key) {
    $this->addressId = $addressId;
    $this->key = $key;
  }
}
