<?php

namespace App\Domain\Address\Events;
use Spatie\EventSourcing\ShouldBeStored;

final class AddressValueChanged implements ShouldBeStored {
  /** @var string */
  public $value;

  /** @var string */
  public $addressId;

  public function __construct(string $addressId, string $value) {
    $this->addressId = $addressId;
    $this->value = $value;
  }
}
