<?php

namespace App\Domain\Contact\Projectors;

use Log;
use Spatie\EventProjector\Projectors\Projector;
use Spatie\EventProjector\Projectors\ProjectsEvents;
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
    $params = [
      'id' => $aggregateUuid,
      'user_id' => $event->userId,
      'name' => $event->name,
      'gender' => $event->gender
    ];
    $logParams = ['params' => $params, 'event' => 'onContactCreated'];

    if (Contact::create($params)) {
      Log::info('Created contact', $logParams);
    } else {
      Log::info('Failed to create contact', $logParams);
    }
  }

  public function onContactDeleted(ContactDeleted $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'event' => 'onContactDeleted'];

    Log::info('Delete contact', $logParams);

    if (! $contact) {
      Log::info('Contact does not exist', $logParams);
      return;
    }

    Log::info('Delete contact notes', array_merge($logParams, ['count' => $contact->notes->count()]));
    Log::info('Delete contact addresses', array_merge($logParams, ['count' => $contact->addresses->count()]));

    foreach($contact->notes as $note) {
      DeleteNote::from(['id' => $note->id])->execute();
    }

    foreach($contact->addresses as $address) {
      DeleteAddress::from(['id' => $address->id])->execute();
    }

    if ($contact->delete()) {
      Log::info('Deleted contact', $logParams);
    } else {
      Log::info('Could not delete contact', $logParams);
    }
  }

  public function onContactNameChanged(ContactNameChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'name' => $event->name, 'event' => 'onContactNameChanged'];

    Log::info('Update contact name', $logParams);

    if (! $contact) {
      Log::info('Contact not found', $logParams);
      return;
    }

    $contact->name = $event->name;

    if ($contact->save()) {
      Log::info('Contact name updated', $logParams);
    } else {
      Log::info('Could not update contact name', $logParams);
    }
  }

  public function onContactGenderChanged(ContactGenderChanged $event, string $aggregateUuid) {
    $contact = Contact::find($aggregateUuid);
    $logParams = ['id' => $aggregateUuid, 'gender' => $event->gender, 'event' => 'onContactGenderChanged'];

    Log::info('Update contact gender', $logParams);

    if (! $contact) {
      Log::info('Contact not found', $logParams);
      return;
    }

    $contact->gender = $event->gender;

    if ($contact->save()) {
      Log::info('Contact gender updated', $logParams);
    } else {
      Log::info('Could not update contact gender', $logParams);
    }
  }
}
