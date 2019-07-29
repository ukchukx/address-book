<?php

namespace App\Domain\Contact;

use Log;
use Spatie\EventProjector\AggregateRoot;
use App\Domain\Contact\Events\ContactCreated;
use App\Domain\Contact\Events\ContactDeleted;
use App\Domain\Contact\Events\ContactNameChanged;
use App\Domain\Contact\Events\ContactGenderChanged;

final class ContactAggregate extends AggregateRoot {
  /** @var string */
  public $name;

  /** @var string */
  public $gender;

  /** @var string */
  public $userId;

  public function createContact(string $userId, string $name, string $gender) {
    Log::info('Command received', [
      'command' => 'createContact',
      'params' => ['userId' => $userId, 'name' => $name, 'gender' => $gender]
    ]);

    $this->recordThat(new ContactCreated($userId, $name, $gender));

    $this->persist();
  }

  public function updateContact(string $contactId, string $name, string $gender) {
    $updated = false;

    Log::info('Command received', [
      'command' => 'updateContact',
      'params' => ['contactId' => $contactId, 'name' => $name, 'gender' => $gender]
    ]);

    if (! empty($name) && $name != $this->name) {
      $this->recordThat(new ContactNameChanged($contactId, $name));

      $updated = true;
    }

    if (! empty($gender) && $gender != $this->gender) {
      $this->recordThat(new ContactGenderChanged($contactId, $gender));

      $updated = true;
    }

    if ($updated) $this->persist();
  }

  public function deleteContact(string $id) {
    Log::info('Command received', ['command' => 'deleteContact', 'params' => ['id' => $id]]);

    $this->recordThat(new ContactDeleted($id));

    $this->persist();
  }

  public function applyContactCreated(ContactCreated $event) {
    Log::info('Applying event', ['event' => 'ContactCreated', 'event' => $event]);

    $this->name = $event->name;
    $this->gender = $event->gender;
    $this->userId = $event->userId;

    $this->persist();
  }

  public function applyContactNameChanged(ContactNameChanged $event) {
    Log::info('Applying event', ['event' => 'ContactNameChanged', 'event' => $event]);

    $this->name = $event->name;

    $this->persist();
  }

  public function applyContactGenderChanged(ContactGenderChanged $event) {
    Log::info('Applying event', ['event' => 'ContactGenderChanged', 'event' => $event]);

    $this->gender = $event->gender;

    $this->persist();
  }
}
