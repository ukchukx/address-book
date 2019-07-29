<?php

namespace App\Domain\Address\Projectors;

use Log;
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
    $params = [
      'id' => $aggregateUuid,
      'contact_id' => $event->contactId,
      'key' => $event->key,
      'value' => $event->value
    ];
    $logParams = ['params' => $params, 'event' => 'onAddressCreated'];

    if (Address::create($params)) {
      Log::info('Created address', $logParams);
    } else {
      Log::info('Failed to create address', $logParams);
    }
  }

  public function onAddressDeleted(AddressDeleted $event, string $aggregateUuid) {
    $logParams = ['id' => $aggregateUuid, 'event' => 'onAddressDeleted'];

    if (Address::where('id', $aggregateUuid)->delete()) {
      Log::info('Deleted address', $logParams);
    } else {
      Log::info('Could not delete address', $logParams);
    }
  }

  public function onAddressKeyChanged(AddressKeyChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'key' => $event->key, 'event' => 'onAddressKeyChanged'];

    if (! $address) {
      Log::info('Address not found', $logParams);
      return;
    }

    $address->key = $event->key;

    if ($address->save()) {
      Log::info('Address key updated', $logParams);
    } else {
      Log::info('Could not update address key', $logParams);
    }
  }

  public function onAddressValueChanged(AddressValueChanged $event, string $aggregateUuid) {
    $address = Address::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'value' => $event->value, 'event' => 'onAddressValueChanged'];

    if (! $address) {
      Log::info('Address not found', $logParams);
      return;
    }

    $address->value = $event->value;

    if ($address->save()) {
      Log::info('Address value updated', $logParams);
    } else {
      Log::info('Could not update address value', $logParams);
    }
  }
}
