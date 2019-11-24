<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\EventSourcing\Facades\Projectionist;
use App\Domain\Address\Projectors\AddressProjector;
use App\Domain\Contact\Projectors\ContactProjector;
use App\Domain\Note\Projectors\NoteProjector;

class ProjectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      Projectionist::addProjectors([
        AddressProjector::class,
        ContactProjector::class,
        NoteProjector::class
      ]);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
