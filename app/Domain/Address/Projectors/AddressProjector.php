<?php

namespace App\Domain\Address\Projectors;

use Ramsey\Uuid\Uuid;
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
    Address::create([
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'key' => $event->key,
      'value' => $event->value
    ]);
  }

  public function onAddressDeleted(AddressDeleted $event, string $aggregateUuid) {
    Address::where('id', $aggregateUuid)->delete();
  }

  public function onAddressKeyChanged(AddressKeyChanged $event, string $aggregateUuid) {
    Address::where('id', $aggregateUuid)->update(['key' => $event->key]);
  }

  public function onAddressValueChanged(AddressValueChanged $event, string $aggregateUuid) {
    Address::where('id', $aggregateUuid)->update(['value' => $event->value]);
  }
}
