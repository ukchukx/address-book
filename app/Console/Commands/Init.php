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
  protected $signature = 'init';

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
    $this->line('<info>Generate oAuth keys...</info>');
    $this->call('passport:install');

    $this->line('<info>Run migrations...</info>');
    $this->call('migrate', ['--force' => true]);

    $this->call('load:serve');
  }
}
