<?php

namespace App\Domain\Address\Projectors;

use Spatie\EventProjector\Projectors\Projector;
use Spatie\EventProjector\Projectors\ProjectsEvents;
use App\Domain\Address\Events\AddressCreated;
use App\Domain\Address\Events\AddressDeleted;
use App\Domain\Address\Events\AddressKeyChanged;
use App\Domain\Address\Events\AddressValueChanged;
use App\Models\Address;

class AddressProjector implements Projector {
  use ProjectsEvents;

  public function onAddressCreated(AddressCreated $event, string $aggregateUuid) {
    Address::store(new Address($aggregateUuid, $event->contactId, $event->key, $event->value));
  }

  public function onAddressDeleted(AddressDeleted $event, string $aggregateUuid) {
    Address::remove($aggregateUuid);
  }

  public function onAddressKeyChanged(AddressKeyChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);
    $address->key = $event->key;
    Address::store($address);
  }

  public function onAddressValueChanged(AddressValueChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);
    $address->value = $event->value;
    Address::store($address);
  }
}
