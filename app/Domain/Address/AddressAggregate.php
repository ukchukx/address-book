<?php

namespace App\Domain\Address;

use Log;
use Spatie\EventProjector\AggregateRoot;
use App\Domain\Address\Events\AddressCreated;
use App\Domain\Address\Events\AddressDeleted;
use App\Domain\Address\Events\AddressKeyChanged;
use App\Domain\Address\Events\AddressValueChanged;

final class AddressAggregate extends AggregateRoot {
  /** @var string */
  public $key;

  /** @var string */
  public $value;

  /** @var string */
  public $contactId;

  public function createAddress(string $contactId, string $key, string $value) {
    Log::info('Command received', [
      'command' => 'createAddress',
      'params' => ['contactId' => $contactId, 'key' => $key, 'value' => $value]
    ]);

    $this->recordThat(new AddressCreated($contactId, $key, $value));

    $this->persist();
  }

  public function updateAddress(string $addressId, string $key, string $value) {
    $updated = false;

    Log::info('Command received', [
      'command' => 'updateAddress',
      'params' => ['addressId' => $addressId, 'key' => $key, 'value' => $value]
    ]);

    if (! empty($key) && $key != $this->key) {
      $this->recordThat(new AddressKeyChanged($addressId, $key));

      $updated = true;
    }

    if (! empty($value) && $value != $this->value) {
      $this->recordThat(new AddressValueChanged($addressId, $value));

      $updated = true;
    }

    if ($updated) $this->persist();
  }

  public function deleteAddress(string $id) {
    Log::info('Command received', ['command' => 'deleteAddress', 'params' => ['id' => $id]]);

    $this->recordThat(new AddressDeleted($id));

    $this->persist();
  }

  public function applyAddressCreated(AddressCreated $event) {
    Log::info('Apply event to aggregate', ['event' => 'AddressCreated', 'event' => $event]);

    $this->key = $event->key;
    $this->value = $event->value;
    $this->contactId = $event->contactId;

    $this->persist();
  }

  public function applyAddressValueChanged(AddressValueChanged $event) {
    Log::info('Apply event to aggregate', ['event' => 'AddressValueChanged', 'event' => $event]);

    $this->value = $event->value;

    $this->persist();
  }

  public function applyAddressKeyChanged(AddressKeyChanged $event) {
    Log::info('Apply event to aggregate', ['event' => 'AddressKeyChanged', 'event' => $event]);

    $this->key = $event->key;

    $this->persist();
  }
}
