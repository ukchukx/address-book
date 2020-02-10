<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\PhpExecutableFinder;
use Illuminate\Support\Facades\Artisan;

class Init extends Command {
  /**
  * The name and signature of the console command.
  *
  * @var string
  */
  protected $signature = 'app:init';

  /**
  * The console command description.
  *
  * @var string
  */
  protected $description = 'Do some housekeeping, then call the command to start the server';

  /**
  * Execute the console command.
  *
  * @return mixed
  */
  public function handle() {
    $this->line('<info>Generate OAuth keys...</info>');

    $this->call('passport:install');

    $this->line('<info>Run migrations...</info>');

    $this->call('migrate', ['--force' => true]);
    $this->call('migrate:refresh', [
      '--force' => true, 
      '--path' => '/database/migrations/2019_07_29_071318_create_contacts_table.php'
    ]);
    $this->call('migrate:refresh', [
      '--force' => true, 
      '--path' => '/database/migrations/2019_07_29_071327_create_notes_table.php'
    ]);
    $this->call('migrate:refresh', [
      '--force' => true, 
      '--path' => '/database/migrations/2019_07_29_071336_create_addresses_table.php'
    ]);
    $this->call('app:serve');
  }
}
