<?php

namespace App\Domain\Contact\Projectors;

use Spatie\EventProjector\Projectors\Projector;
use Spatie\EventProjector\Projectors\ProjectsEvents;
use App\Domain\Contact\Events\ContactCreated;
use App\Domain\Contact\Events\ContactDeleted;
use App\Domain\Contact\Events\ContactNameChanged;
use App\Domain\Contact\Events\ContactGenderChanged;
use App\Models\Contact;

class ContactProjector implements Projector {
  use ProjectsEvents;

  public function onContactCreated(ContactCreated $event, string $aggregateUuid) {
    Contact::store(new Contact($aggregateUuid, $event->userId, $event->name, $event->gender, [], []));
  }

  public function onContactDeleted(ContactDeleted $event, string $aggregateUuid) {
    Contact::remove($aggregateUuid);
  }

  public function onContactNameChanged(ContactNameChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);
    $contact->name = $event->name;
    Contact::store($contact);
  }

  public function onContactGenderChanged(ContactGenderChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);
    $contact->gender = $event->gender;
    Contact::store($contact);
  }
}
