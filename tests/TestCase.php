<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Contact;

abstract class TestCase extends BaseTestCase {
    use CreatesApplication, RefreshDatabase;
    
    private $faker;

    private function getFaker() {
      if (empty($this->faker)) $this->faker = Faker::create();
      
      return $this->faker;
    }

    protected function setUp(): void {
      parent::setUp();
    }

    protected function getUserAttributes() {
      $faker = $this->getFaker();

      return [
        'id' => Str::uuid(),
        'email' => $faker->safeEmail,
        'name' => $faker->name,
        'password' => $faker->password
      ];
    }

    protected function getContactAttributes(User $user) {
      $faker = $this->getFaker();

      return [
        'user_id' => $user->id,
        'name' => $faker->name,
        'gender' => rand() % 2 == 0 ? 'male' : 'female'
      ];
    }

    protected function getNoteAttributes(Contact $contact) {
      $faker = $this->getFaker();

      return [
        'contact_id' => $contact->id,
        'title' => $faker->sentence,
        'text' => $faker->text
      ];
    }

    protected function getAddressAttributes(Contact $contact, $key) {
      $faker = $this->getFaker();

      if ($key == 'email') $value = $faker->safeEmail;
      if ($key == 'phone') $value = $faker->e164PhoneNumber;
      if ($key == 'physical') $value = $faker->sentence;

      return [
        'contact_id' => $contact->id,
        'value' => $value,
        'key' => $key
      ];
    }
}
