<?php

return [

    /*
     * These directories will be scanned for Projectors and Reactors. They
     * will be registered to Projectionist automatically.
     */
    'auto_discover_projectors_and_reactors' => [
        app_path(),
    ],

    /*
     * Projectors are classes that build up projections. You can create them by performing
     * `php artisan event-projector:create-projector`. When not using auto-discovery,
     * Projectors can be registered in this array or a service provider.
     */
    'projectors' => [
        // App\Projectors\YourProjector::class
    ],

    /*
     * Reactors are classes that handle side-effects. You can create them by performing
     * `php artisan event-projector:create-reactor`. When not using auto-discovery
     * Reactors can be registered in this array or a service provider.
     */
    'reactors' => [
        // App\Reactors\YourReactor::class
    ],

    /*
     * A queue is used to guarantee that all events get passed to the projectors in
     * the right order. Here you can set of the name of the queue.
     */
    'queue' => env('EVENT_PROJECTOR_QUEUE_NAME', null),

    /*
     * When a Projector or Reactor throws an exception the event Projectionist can catch it
     * so all other projectors and reactors can still do their work. The exception will
     * be passed to the `handleException` method on that Projector or Reactor.
     */
    'catch_exceptions' => env('EVENT_PROJECTOR_CATCH_EXCEPTIONS', false),

    /*
     * This class is responsible for storing events. To add extra behaviour you
     * can change this to a class of your own. The only restriction is that
     * it should extend \Spatie\EventProjector\Models\StoredEvent.
     */
    'stored_event_model' => \Spatie\EventProjector\Models\StoredEvent::class,

    /*
     * This class is responsible for handling stored events. To add extra behaviour you
     * can change this to a class of your own. The only restriction is that
     * it should extend \Spatie\EventProjector\HandleDomainEventJob.
     */
    'stored_event_job' => \Spatie\EventProjector\HandleStoredEventJob::class,

    /*
     * This class is responsible for serializing events. By default an event will be serialized
     * and stored as json. You can customize the class name. A valid serializer
     * should implement Spatie\EventProjector\EventSerializers\Serializer.
     */
    'event_serializer' => \Spatie\EventProjector\EventSerializers\JsonEventSerializer::class,

    /*
     * When replaying events, potentially a lot of events will have to be retrieved.
     * In order to avoid memory problems events will be retrieved as chunks.
     * You can specify the chunk size here.
     */
    'replay_chunk_size' => 1000,

    /*
     * In production, you likely don't want the package to auto-discover the event handlers
     * on every request. The package can cache all registered event handlers.
     * More info: https://docs.spatie.be/laravel-event-projector/v2/advanced-usage/discovering-projectors-and-reactors
     *
     * Here you can specify where the cache should be stored.
     */
    'cache_path' => storage_path('app/event-projector'),
];
