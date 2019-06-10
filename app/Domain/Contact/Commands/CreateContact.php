<?php

namespace App\Domain\Contact\Commands;

use Ramsey\Uuid\Uuid;
use App\Models\User;
use App\Models\Contact;
use App\Domain\Command;
use App\Domain\Contact\ContactAggregate;

final class CreateContact extends Command {
  public function __construct(array $bag) {
    $this->attributes['user_id'] = Uuid::isValid($bag['user_id']) ?
      $bag['user_id'] :
      Uuid::fromString($bag['user_id']);

    $this->attributes['name'] = $bag['name'];
    $this->attributes['gender'] = strtolower($bag['gender']);
  }

  public static function from(array $bag) : CreateContact {
    return new CreateContact($bag);
  }

  public function isValid() : bool {
    return ((bool) User::find($this->attributes['user_id'])) &&
      ! empty($this->attributes['name']) &&
      in_array($this->attributes['gender'], ['male', 'female']);
  }

  public function execute() : ?Contact {
    if (! $this->isValid()) return null;

    $newId = Uuid::uuid4();

    ContactAggregate::retrieve($newId)
      ->createContact($this->attributes['user_id'], $this->attributes['name'], $this->attributes['gender']);

    return Contact::find($newId);
  }
}
