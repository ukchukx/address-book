<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;

abstract class TestCase extends BaseTestCase {
    use CreatesApplication;

    private $faker;

    protected function getUserAttributes() {
      if (empty($this->faker)) $this->faker = Faker::create();

      return [
        'id' => Str::uuid(),
        'email' => $this->faker->safeEmail,
        'name' => $this->faker->name,
        'password' => $this->faker->password
      ];
    }

    protected function getContactAttributes(User $user) {
      if (empty($this->faker)) $this->faker = Faker::create();

      return [
        'user_id' => $user->id,
        'name' => $this->faker->name,
        'gender' => rand() % 2 == 0 ? 'male' : 'female'
      ];
    }
}
