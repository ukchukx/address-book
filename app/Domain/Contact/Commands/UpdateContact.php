<?php

namespace App\Domain\Contact\Commands;

use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Contact;
use App\Domain\Command;
use App\Domain\Contact\ContactAggregate;

final class UpdateContact extends Command {
  public function __construct(array $bag) {
    $this->attributes['contact_id'] = Uuid::isValid($bag['contact_id']) ?
      $bag['contact_id'] :
      Uuid::fromString($bag['contact_id']);

    if (! empty($bag['name'])) $this->attributes['name'] = $bag['name'];
    if (! empty($bag['gender'])) $this->attributes['gender'] = strtolower($bag['gender']);
  }

  public static function from(array $bag) : UpdateContact {
    return new UpdateContact($bag);
  }

  public function isValid() : bool {
    $contactExists = ((bool) Contact::find($this->attributes['contact_id']));
    $nameOk = ! empty($this->attributes['name']) || true; // Ignore if not provided
    $genderOk = ! empty($this->attributes['gender']) ?
      in_array($this->attributes['gender'], ['male', 'female']) : // If provided, check validity
      true; // Ignore if not provided

    return $contactExists && $nameOk && $genderOk;
  }

  public function execute() : ?Contact {
    if (! $this->isValid()) return null;

    ContactAggregate::retrieve($this->attributes['contact_id'])
      ->updateContact(
        $this->attributes['contact_id'],
        ! empty($this->attributes['name']) ? $this->attributes['name'] : '',
        ! empty($this->attributes['gender']) ? $this->attributes['gender'] : ''
      );

    return Contact::find($this->attributes['contact_id']);
  }
}
