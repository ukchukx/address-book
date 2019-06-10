<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model {
  use Traits\UsesUuid;

  protected $guarded = [];

  public $incrementing = false;

  public function user() {
    return $this->belongsTo(User::class);
  }
}
