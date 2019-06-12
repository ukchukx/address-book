<?php

namespace App\Domain\Address\Commands;

use Ramsey\Uuid\Uuid;
use App\Models\Address;
use App\Domain\Command;
use App\Domain\Address\AddressAggregate;

final class UpdateAddress extends Command {
  public function __construct(array $bag) {
    $this->attributes['address_id'] = Uuid::isValid($bag['address_id']) ?
      $bag['address_id'] :
      Uuid::fromString($bag['address_id']);

    if (! empty($bag['key'])) $this->attributes['key'] = $bag['key'];
    if (! empty($bag['value'])) $this->attributes['value'] = $bag['value'];
  }

  public static function from(array $bag) : UpdateAddress {
    return new UpdateAddress($bag);
  }

  public function isValid() : bool {
    $noteExists = ((bool) Address::find($this->attributes['address_id']));
    // Ignore if not provided
    $keyOk = empty($this->attributes['key']) ? true :
      in_array($this->attributes['key'], ['phone', 'email', 'physical']);
    $valueOk = ! empty($this->attributes['value']) || true;

    return $noteExists && $keyOk && $valueOk && $this->isFormatValid();
  }

  private function isFormatValid() : bool {
    if ($this->attributes['key'] == 'physical') return true; // $valueOk in isValid will suffice
    if ($this->attributes['key'] == 'phone') return preg_match('/^[+]?\d+$/', $this->attributes['value']);
    if ($this->attributes['key'] == 'email') return preg_match('/^\w+\.*\w*@\w+\.\w+/', $this->attributes['value']);
  }

  public function execute() : ?Address {
    if (! $this->isValid()) return null;

    AddressAggregate::retrieve($this->attributes['address_id'])
      ->updateAddress(
        $this->attributes['address_id'],
        ! empty($this->attributes['key']) ? $this->attributes['key'] : '',
        ! empty($this->attributes['value']) ? $this->attributes['value'] : ''
      );

    return Address::find($this->attributes['address_id']);
  }
}
