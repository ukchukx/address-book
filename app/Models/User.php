<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Redis;

class User extends Authenticatable {
  use Traits\UsesUuid, HasApiTokens, Notifiable;

  protected $guarded = [];

  public $incrementing = false;

  /**
   * The attributes that should be hidden for arrays.
   *
   * @var array
   */
  protected $hidden = [
    'password', 'remember_token',
  ];

  public function contacts() {
    $userKey = static::key($this->id);

    return Redis::exists($userKey) ? Redis::mget(Redis::get($userKey)) : [];
  }

  public function findContact(string $id) : ?Contact {
    return array_reduce($this->contacts(), function ($contact, $c) use($id) { return $c->id === $id ? $c : $contact; }, null);
  }

  public static function storeContact(string $id, string $contactKey) {
    $userKey = static::key($id);
    $contactKeys = Redis::exists($userKey) ? Redis::get($userKey) : [];

    $contactKeys = array_filter($contactKeys, function ($k) use($contactKey) { return $k !== $contactKey; });

    return Redis::set($userKey, array_merge($contactKeys, [$contactKey]));
  }

  public static function removeContact(string $id, string $contactKey) {
    $userKey = static::key($id);
    $contacts = array_filter(Redis::get($userKey), function ($k) use($contactKey) { return $k !== $contactKey; });

    return count($contacts) ? Redis::set($userKey, $contacts) : Redis::del($userKey);
  }

  private static function key(string $id) : string {
    return "user:$id";
  }
}
