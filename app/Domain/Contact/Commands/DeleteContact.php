<?php

namespace App\Domain\Contact\Commands;

use Ramsey\Uuid\Uuid;
use App\Models\Contact;
use App\Domain\Command;
use App\Domain\Contact\ContactAggregate;

final class DeleteContact extends Command {
  public function __construct(array $bag) {
    $this->attributes['id'] = Uuid::isValid($bag['id']) ? $bag['id'] : Uuid::fromString($bag['id']);
  }

  public static function from(array $bag) : DeleteContact {
    return new DeleteContact($bag);
  }

  public function isValid() : bool {
    return (bool) Contact::find($this->attributes['id']);
  }

  public function execute() {
    if (! $this->isValid()) return;

    ContactAggregate::retrieve($this->attributes['id'])->deleteContact($this->attributes['id']);
  }
}
