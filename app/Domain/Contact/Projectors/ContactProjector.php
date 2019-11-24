<?php

namespace App\Domain\Contact\Projectors;

use Log;
use Spatie\EventSourcing\Projectors\Projector;
use Spatie\EventSourcing\Projectors\ProjectsEvents;
use App\Domain\Contact\Events\ContactCreated;
use App\Domain\Contact\Events\ContactDeleted;
use App\Domain\Contact\Events\ContactNameChanged;
use App\Domain\Contact\Events\ContactGenderChanged;
use App\Models\Contact;
use App\Domain\Address\Commands\DeleteAddress;
use App\Domain\Note\Commands\DeleteNote;

class ContactProjector implements Projector {
  use ProjectsEvents;

  public function onContactCreated(ContactCreated $event, string $aggregateUuid) {
    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    Contact::updateOrCreate(
      ['id' => $aggregateUuid],
      [
        'user_id' => $event->userId,
        'name' => $event->name,
        'gender' => $event->gender
      ]);
  }

  public function onContactDeleted(ContactDeleted $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    foreach($contact->notes as $note) DeleteNote::from(['id' => $note->id])->execute();

    foreach($contact->addresses as $address) DeleteAddress::from(['id' => $address->id])->execute();

    $contact->delete();
  }

  public function onContactNameChanged(ContactNameChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $contact->name = $event->name;
    $contact->save();
  }

  public function onContactGenderChanged(ContactGenderChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);

    Log::info('Project event', ['id' => $aggregateUuid, 'event' => $event]);

    $contact->gender = $event->gender;
    $contact->save();
  }
}
