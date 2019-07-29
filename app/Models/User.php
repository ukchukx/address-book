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
    return $this->hasMany(Contact::class);
  }

  public function findContact(string $id) : ?Contact {
    return $this->contacts()->where('id', $id)->first();
  }
}
