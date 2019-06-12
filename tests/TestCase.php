<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Contact;

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

    protected function getNoteAttributes(Contact $contact) {
      if (empty($this->faker)) $this->faker = Faker::create();

      return [
        'contact_id' => $contact->id,
        'title' => $this->faker->sentence,
        'text' => $this->faker->text
      ];
    }

    protected function getAddressAttributes(Contact $contact, $key) {
      if (empty($this->faker)) $this->faker = Faker::create();

      if ($key == 'email') $value = $this->faker->safeEmail;
      if ($key == 'phone') $value = $this->faker->e164PhoneNumber;
      if ($key == 'physical') $value = $this->faker->sentence;

      return [
        'contact_id' => $contact->id,
        'value' => $value,
        'key' => $key
      ];
    }
}
