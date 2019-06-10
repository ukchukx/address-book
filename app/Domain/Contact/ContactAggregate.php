<?php

namespace App\Domain\Contact;

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
    $this->recordThat(new ContactCreated($userId, $name, $gender));

    $this->persist();
  }

  public function updateContact(string $contactId, string $name, string $gender) {
    $updated = false;

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
    $this->recordThat(new ContactDeleted($id));

    $this->persist();
  }

  public function applyContactCreated(ContactCreated $event) {
    $this->name = $event->name;
    $this->gender = $event->gender;
    $this->userId = $event->userId;

    $this->persist();
  }

  public function applyContactNameChanged(ContactNameChanged $event) {
    $this->name = $event->name;

    $this->persist();
  }

  public function applyContactGenderChanged(ContactGenderChanged $event) {
    $this->gender = $event->gender;

    $this->persist();
  }
}
