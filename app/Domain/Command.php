<?php

namespace App\Domain;

abstract class Command {
  /** @var array */
  protected $attributes;

  abstract public function isValid() : bool;

  abstract public function execute();

  public function getAttributes() : array {
    return $this->attributes;
  }
}
