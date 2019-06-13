<?php

namespace App\Models;

use Illuminate\Support\Facades\Redis;


class Note {
  /** @var string */
  public $id;
  /** @var string */
  public $contact_id;
  /** @var string */
  public $title;
  /** @var string */
  public $text;

  public function __construct(string $id, string $contactId, string $title, string $text) {
    $this->id = $id;
    $this->contact_id = $contactId;
    $this->title = $title;
    $this->text = $text;
  }

  public function contact() : ?Contact {
    return Contact::find($this->contact_id);
  }

  public static function find(string $id) : ?Note {
    // First, get the containing contact
    $contact = Contact::find(Redis::get(static::key($id)));
    $notes = empty($contact) ? [] : $contact->notes;

    // Find and return the note with the supplied id
    return array_reduce(
      $notes,
      function ($currNote, $n) use($id) { return $n->id === $id ? $n : $currNote; },
      null
    );
  }

  public static function store(Note $note) {
    $contact = Contact::find($note->contact_id);

    if ($contact) {
      // First, get other notes...
      $notes = array_filter($contact->notes, function ($n) use($note) { return $n->id !== $note->id; });
      // ...then, add this note.
      $contact->notes = array_merge($notes, [$note]);

      Contact::store($contact);
      // Since notes are nested in contacts we store a pointer to the containing contact.
      return Redis::set(static::key($note->id), $note->contact_id);
    }

    return false;
  }

  public static function remove(string $id) {
    $key = static::key($id);
    // First, get the containing contact
    $contact = Contact::find(Redis::get($key));

    if ($contact) {
      // Remove this note from the array
      $contact->notes = array_filter($contact->notes, function ($n) use($id) { return $n->id !== $id; });

      Contact::store($contact);

      // Remove pointer
      return Redis::del($key);
    }

    return false;
  }

  public static function key(string $id) : string {
    return "note:$id";
  }
}
