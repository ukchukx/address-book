<?php

namespace App\Domain\Address\Commands;

use Ramsey\Uuid\Uuid;
use App\Domain\Command;
use App\Domain\Address\AddressAggregate;
use App\Models\Address;
use App\Models\Contact;

final class CreateAddress extends Command {
  public function __construct(array $bag) {
    $this->attributes['contact_id'] = Uuid::isValid($bag['contact_id']) ?
      $bag['contact_id'] :
      Uuid::fromString($bag['contact_id']);

    $this->attributes['key'] = $bag['key'];
    $this->attributes['value'] = $bag['value'];
  }

  public static function from(array $bag) : CreateAddress {
    return new CreateAddress($bag);
  }

  public function isValid() : bool {
    return ((bool) Contact::find($this->attributes['contact_id'])) &&
      in_array($this->attributes['key'], ['phone', 'email', 'physical']) &&
      ! empty($this->attributes['value']) &&
      $this->isFormatValid();
  }

  private function isFormatValid() : bool {
    if ($this->attributes['key'] == 'physical') return true; // empty check in isValid will suffice
    if ($this->attributes['key'] == 'phone') return preg_match('/^[+]?\d+$/', $this->attributes['value']);
    if ($this->attributes['key'] == 'email') return preg_match('/^\w+\.*\w*@\w+\.\w+/', $this->attributes['value']);
  }

  public function execute() : ?Address {
    if (! $this->isValid()) return null;

    $newId = Uuid::uuid4();

    AddressAggregate::retrieve($newId)
      ->createAddress($this->attributes['contact_id'], $this->attributes['key'], $this->attributes['value']);

    return Address::find($newId);
  }
}
