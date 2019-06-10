<?php

namespace App\Models\Traits;

use Ramsey\Uuid\Uuid;

trait UsesUuid {
  protected static function bootUsesUuid() {
    static::creating(function ($model) {
      if ((! $model->incrementing) && empty($model->{$model->getKeyName()})) {
        $model->{$model->getKeyName()} = (string) Uuid::uuid4();
      }
    });
  }

  public function getKeyType() {
    return 'string';
  }
}
