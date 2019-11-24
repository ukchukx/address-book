<?php

namespace App\Domain\Address\Projectors;

use Log;
use Spatie\EventSourcing\Projectors\Projector;
use Spatie\EventSourcing\Projectors\ProjectsEvents;
use App\Domain\Address\Events\AddressCreated;
use App\Domain\Address\Events\AddressDeleted;
use App\Domain\Address\Events\AddressKeyChanged;
use App\Domain\Address\Events\AddressValueChanged;
use App\Models\Address;

class AddressProjector implements Projector {
  use ProjectsEvents;

  public function onAddressCreated(AddressCreated $event, string $aggregateUuid) {
    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    Address::create([
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'key' => $event->key,
      'value' => $event->value
    ]);
  }

  public function onAddressDeleted(AddressDeleted $event, string $aggregateUuid) {
    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    Address::where('id', $aggregateUuid)->delete();
  }

  public function onAddressKeyChanged(AddressKeyChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $address->key = $event->key;
    $address->save();
  }

  public function onAddressValueChanged(AddressValueChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $address->value = $event->value;
    $address->save();
  }
}
