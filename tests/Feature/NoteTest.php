<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domain\Note\Commands\CreateNote;
use App\Domain\Note\Commands\UpdateNote;
use App\Domain\Note\Commands\DeleteNote;
use App\Domain\Contact\Commands\CreateContact;
use App\Models\User;
use App\Models\Note;

class NoteTest extends TestCase {
  use RefreshDatabase;

  public function testNoteCreation() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $data = $this->getNoteAttributes($contact);
    $createNoteCommand = CreateNote::from($data);

    $this->assertEquals(count($createNoteCommand->getAttributes()), count($data));
    $this->assertTrue($createNoteCommand->isValid());

    $note = $createNoteCommand->execute();

    $this->assertTrue($note instanceof Note);
    $this->assertEquals($note->title, $data['title']);
    $this->assertEquals($note->text, $data['text']);
    $this->assertEquals($note->contact_id, $contact->id);
  }

  public function testNoteDeletion() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $note = CreateNote::from($this->getNoteAttributes($contact))->execute();

    $this->assertEquals(1, count($contact->notes()->get()));

    $command = DeleteNote::from(['id' => $note->id]);

    $this->assertTrue($command->isValid());

    $command->execute();

    $this->assertEquals(0, count($contact->notes()->get()));
  }

  public function testNoteTitleUpdate() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $note = CreateNote::from($this->getNoteAttributes($contact))->execute();
    $data = ['note_id' => $note->id];
    $data['title'] = $this->getNoteAttributes($contact)['title'];
    $command = UpdateNote::from($data);

    $this->assertTrue($command->isValid());

    $updatedNote = $command->execute();

    $this->assertNotEquals($updatedNote->title, $note->title);
    $this->assertEquals($updatedNote->title, $data['title']);
    $this->assertEquals($updatedNote->text, $note->text);
  }

  public function testNoteTextUpdate() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $note = CreateNote::from($this->getNoteAttributes($contact))->execute();
    $data = ['note_id' => $note->id];
    $data['text'] = $this->getNoteAttributes($contact)['text'];
    $command = UpdateNote::from($data);

    $this->assertTrue($command->isValid());

    $updatedNote = $command->execute();

    $this->assertNotEquals($updatedNote->text, $note->text);
    $this->assertEquals($updatedNote->text, $data['text']);
    $this->assertEquals($updatedNote->title, $note->title);
  }

  public function testNoteUpdateWithUnchangedData() {
    $user = User::create($this->getUserAttributes());
    $contact = CreateContact::from($this->getContactAttributes($user))->execute();
    $note = CreateNote::from($this->getNoteAttributes($contact))->execute();
    $data = ['note_id' => $note->id, 'title' => $note->title, 'text' => $note->text];

    $command = UpdateNote::from($data);

    $this->assertTrue($command->isValid());

    $updatedNote = $command->execute();

    $this->assertEquals($updatedNote->updated_at, $note->updated_at);
  }
}
