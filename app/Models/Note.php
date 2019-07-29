<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model {
  use Traits\UsesUuid;

  protected $guarded = [];
  public $incrementing = false;

  public function contact() {
    return $this->belongsTo(Contact::class);
  }
}
