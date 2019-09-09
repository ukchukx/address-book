<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\EventProjector\Projectionist;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Note;

class LoadStoreAndServe extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'address-book:serve';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Load read store, then start the server';

  /** @var \Spatie\EventProjector\Projectionist */
  protected $projectionist;

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct(Projectionist $projectionist) {
    parent::__construct();

    $this->projectionist = $projectionist;
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle() {
    $this->line("<info>Truncate read tables...</info>");
    $this->truncateTables();

    $this->line("<info>Build read model...</info>");
    $this->replay();

    return $this->call('serve', ['--host' => env('APP_HOST', '0.0.0.0')]);
  }

  private function truncateTables(): void {
    Contact::query()->delete();
    Note::query()->delete();
    Address::query()->delete();
  }

  private function replay(): void {
    $storeEventClass = config('event-projector.stored_event_model');

    $replayCount = $storeEventClass::startingFrom(0)->count();

    if ($replayCount === 0) {
      $this->warn('There are no events to replay');
      return;
    }

    $this->comment("Replaying $replayCount events...");

    $bar = $this->output->createProgressBar($storeEventClass::count());

    $this->projectionist->replay($this->projectionist->getProjectors(), 0, function () use ($bar) { $bar->advance(); });
    $bar->finish();
    $this->emptyLine(2);
  }

  private function emptyLine(int $amount = 1): void {
    foreach (range(1, $amount) as $i) $this->line('');
  }
}
