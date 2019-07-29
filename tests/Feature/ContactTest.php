<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Contact\Commands\CreateContact;
use App\Domain\Contact\Commands\DeleteContact;
use App\Domain\Contact\Commands\UpdateContact;
use App\Models\User;
use App\Models\Contact;

class ContactTest extends TestCase {
  use RefreshDatabase;

  public function testContactCreation() {
    $user = User::create($this->getUserAttributes());
    $data = $this->getContactAttributes($user);
    $createContactCommand = CreateContact::from($data);

    $this->assertEquals(count($createContactCommand->getAttributes()), count($data));
    $this->assertTrue($createContactCommand->isValid());

    $contact = $createContactCommand->execute();

    $this->assertTrue($contact instanceof Contact);
    $this->assertEquals($contact->name, $data['name']);
    $this->assertEquals($contact->gender, $data['gender']);
    $this->assertEquals($contact->user_id, $user->id);
  }

  public function testContactDeletion() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();

    $this->assertEquals(1, $user->contacts()->count());

    $command = DeleteContact::from(['id' => $contact->id]);

    $this->assertTrue($command->isValid());

    $command->execute();

    $this->assertEquals(0, $user->contacts()->count());
  }

  public function testContactNameUpdate() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = ['contact_id' => $contact->id];
    $data['name'] = $this->getContactAttributes($user)['name'];
    $command = UpdateContact::from($data);

    $this->assertTrue($command->isValid());

    $updatedContact = $command->execute();

    $this->assertNotEquals($updatedContact->name, $contact->name);
    $this->assertEquals($updatedContact->name, $data['name']);
    $this->assertEquals($updatedContact->gender, $contact->gender);
  }

  public function testContactGenderUpdate() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = ['contact_id' => $contact->id];
    $genderParam = $this->getContactAttributes($user)['gender'];

    // Since we're randomly choosing genders, we want to continue choosing till we get a gender that's different from that of the contact
    while ($genderParam == $contact->gender) $genderParam = $this->getContactAttributes($user)['gender'];

    $data['gender'] = $genderParam;
    $command = UpdateContact::from($data);

    $this->assertTrue($command->isValid());

    $updatedContact = $command->execute();

    $this->assertNotEquals($updatedContact->gender, $contact->gender);
    $this->assertEquals($updatedContact->gender, $data['gender']);
    $this->assertEquals($updatedContact->name, $contact->name);
  }

  public function testContactUpdateWithUnchangedData() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = ['contact_id' => $contact->id, 'name' => $contact->name, 'gender' => $contact->gender];

    $command = UpdateContact::from($data);

    $this->assertTrue($command->isValid());

    $updatedContact = $command->execute();

    $this->assertEquals($updatedContact->name, $contact->name);
    $this->assertEquals($updatedContact->gender, $contact->gender);
  }
}
