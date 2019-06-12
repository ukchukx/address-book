<?php

namespace App\Domain\Address\Commands;

use Ramsey\Uuid\Uuid;
use App\Domain\Command;
use App\Domain\Address\AddressAggregate;
use App\Models\Address;

final class DeleteAddress extends Command {
  public function __construct(array $bag) {
    $this->attributes['id'] = Uuid::isValid($bag['id']) ? $bag['id'] : Uuid::fromString($bag['id']);
  }

  public static function from(array $bag) : DeleteAddress {
    return new DeleteAddress($bag);
  }

  public function isValid() : bool {
    return (bool) Address::find($this->attributes['id']);
  }

  public function execute() {
    if (! $this->isValid()) return;

    AddressAggregate::retrieve($this->attributes['id'])->deleteAddress($this->attributes['id']);
  }
}
