<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;

class Address {
  /** @var string */
  public $id;
  /** @var string */
  public $contact_id;
  /** @var string */
  public $key;
  /** @var string */
  public $value;

  public function __construct(string $id, string $contactId, string $key, string $value) {
    $this->id = $id;
    $this->contact_id = $contactId;
    $this->key = $key;
    $this->value = $value;
  }

  public function contact() : ?Contact {
    return Contact::find($this->contact_id);
  }

  public static function find(string $id) : ?Address {
    // First, get the containing contact
    $contact = Contact::find(Redis::get(static::key($id)));
    $addresses = empty($contact) ? [] : $contact->addresses;

    // Find and return the address with the supplied id
    return array_reduce(
      $addresses,
      function ($currAddress, $a) use($id) { return $a->id === $id ? $a : $currAddress; },
      null
    );
  }

  public static function store(Address $address) {
    $contact = Contact::find($address->contact_id);

    if ($contact) {
      // First, get other addresses...
      $addresses = array_filter($contact->addresses, function ($n) use($address) { return $n->id !== $address->id; });
      // ...then, add this address.
      $contact->addresses = array_merge($addresses, [$address]);

      Contact::store($contact);
      // Since addresses are nested in contacts we store a pointer to the containing contact.
      return Redis::set(static::key($address->id), $address->contact_id);
    }

    return false;
  }

  public static function remove(string $id) {
    $key = static::key($id);
    // First, get the containing contact
    $contact = Contact::find(Redis::get($key));

    if ($contact) {
      // Remove this address from the array
      $contact->addresses = array_filter($contact->addresses, function ($a) use($id) { return $a->id !== $id; });

      Contact::store($contact);

      // Remove pointer
      return Redis::del($key);
    }

    return false;
  }

  public static function key(string $id) : string {
    return "address:$id";
  }
}
