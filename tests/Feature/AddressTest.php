<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Address\Commands\CreateAddress;
use App\Domain\Address\Commands\UpdateAddress;
use App\Domain\Address\Commands\DeleteAddress;
use App\Domain\Contact\Commands\CreateContact;
use App\Models\User;
use App\Models\Contact;
use App\Models\Address;

class AddressTest extends TestCase {
  use RefreshDatabase;

  public function testEmailAddressCreation() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = $this->getAddressAttributes($contact, 'email');
    $createAddressCommand = CreateAddress::from($data);

    $this->assertEquals(count($createAddressCommand->getAttributes()), count($data));
    $this->assertTrue($createAddressCommand->isValid());

    $address = $createAddressCommand->execute();

    $this->assertTrue($address instanceof Address);
    $this->assertEquals($address->key, $data['key']);
    $this->assertEquals($address->value, $data['value']);
    $this->assertEquals($address->contact_id, $contact->id);
  }

  public function testPhoneAddressCreation() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = $this->getAddressAttributes($contact, 'phone');
    $createAddressCommand = CreateAddress::from($data);

    $this->assertEquals(count($createAddressCommand->getAttributes()), count($data));
    $this->assertTrue($createAddressCommand->isValid());

    $address = $createAddressCommand->execute();

    $this->assertTrue($address instanceof Address);
    $this->assertEquals($address->key, $data['key']);
    $this->assertEquals($address->value, $data['value']);
    $this->assertEquals($address->contact_id, $contact->id);
  }

  public function testPhysicalAddressCreation() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = $this->getAddressAttributes($contact, 'physical');
    $createAddressCommand = CreateAddress::from($data);

    $this->assertEquals(count($createAddressCommand->getAttributes()), count($data));
    $this->assertTrue($createAddressCommand->isValid());

    $address = $createAddressCommand->execute();

    $this->assertTrue($address instanceof Address);
    $this->assertEquals($address->key, $data['key']);
    $this->assertEquals($address->value, $data['value']);
    $this->assertEquals($address->contact_id, $contact->id);
  }

  public function testAddressDeletion() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $address = CreateAddress::from($this->getAddressAttributes($contact, 'email'))->execute();

    $this->assertEquals(1, count(Contact::find($contact->id)->addresses()));

    $command = DeleteAddress::from(['id' => $address->id]);

    $this->assertTrue($command->isValid());

    $command->execute();

    $this->assertEquals(0, count(Contact::find($contact->id)->addresses()));
  }

  public function testAddressUpdate() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $address = CreateAddress::from($this->getAddressAttributes($contact, 'email'))->execute();
    $data = ['address_id' => $address->id, 'key' => 'phone'];
    $data['value'] = $this->getAddressAttributes($contact, 'phone')['value'];
    $command = UpdateAddress::from($data);

    $this->assertTrue($command->isValid());

    $updatedAddress = $command->execute();

    $this->assertNotEquals($updatedAddress->key, $address->key);
    $this->assertNotEquals($updatedAddress->value, $address->value);
    $this->assertEquals($updatedAddress->key, $data['key']);
    $this->assertEquals($updatedAddress->value, $data['value']);
  }

  public function testAddressUpdateFails() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $address = CreateAddress::from($this->getAddressAttributes($contact, 'physical'))->execute();
    $data = ['address_id' => $address->id, 'key' => 'email', 'value' => $address->value];

    $command = UpdateAddress::from($data);

    $this->assertFalse($command->isValid());
    $this->assertNull($command->execute());
  }
}
