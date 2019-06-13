<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;

class Contact {
  /** @var string */
  public $id;
  /** @var string */
  public $user_id;
  /** @var string */
  public $name;
  /** @var string */
  public $gender;
  /** @var array */
  public $notes;
  /** @var array */
  public $addresses;

  public function __construct(string $id,
    string $userId,
    string $name,
    string $gender,
    array $notes,
    array $addresses) {
    $this->id = $id;
    $this->user_id = $userId;
    $this->name = $name;
    $this->gender = $gender;
    $this->notes = $notes;
    $this->addresses = $addresses;
  }

  public function user() : ?User {
    return User::find($this->user_id);
  }

  public function notes() : array {
    return $this->notes;
  }

  public function addresses() : array {
    return $this->addresses;
  }

  public static function find(string $id) : ?Contact {
    return Redis::get(static::key($id));
  }

  public static function store(Contact $contact) {
    if (! Redis::exists(static::key($contact->id))) User::storeContact($contact->user_id, static::key($contact->id));

    return Redis::set(static::key($contact->id), $contact);
  }

  public static function remove(string $id) {
    $contact = static::find($id);

    if ($contact) {
      $key = static::key($id);

      User::removeContact($contact->user_id, $key);

      // Remove note pointers
      foreach($contact->notes as $note) Redis::del(Note::key($note->id));
      // Remove address pointers
      foreach($contact->addresses as $address) Redis::del(Address::key($address->id));

      return Redis::del($key);
    }

    return false;
  }

  private static function key(string $id) : string {
    return "contact:$id";
  }
}
