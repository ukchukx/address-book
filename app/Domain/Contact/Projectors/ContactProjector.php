<?php

namespace App\Domain\Contact\Projectors;

use Ramsey\Uuid\Uuid;
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
    Contact::create([
      'id' => $aggregateUuid,
      'gender' => $event->gender,
      'name' => $event->name,
      'user_id' => $event->userId
    ]);
  }

  public function onContactDeleted(ContactDeleted $event, string $aggregateUuid) {
    Contact::where('id', $aggregateUuid)->delete();
  }

  public function onContactNameChanged(ContactNameChanged $event, string $aggregateUuid) {
    Contact::where('id', $aggregateUuid)->update([
      'name' => $event->name
    ]);
  }

  public function onContactGenderChanged(ContactGenderChanged $event, string $aggregateUuid) {
    Contact::where('id', $aggregateUuid)->update([
      'gender' => $event->gender
    ]);
  }
}
