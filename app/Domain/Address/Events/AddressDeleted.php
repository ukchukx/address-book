<?php

namespace App\Domain\Address\Events;
use Spatie\EventProjector\ShouldBeStored;

final class AddressDeleted implements ShouldBeStored {
  /** @var string */
  public $addressId;

  public function __construct(string $addressId) {
    $this->addressId = $addressId;
  }
}
