<?php

namespace Winata\Core\Response\Listeners\OnErrorEvent;

use Winata\Core\Response\Models\Exception;

class StoreToDatabase
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $inputs = getFillableAttribute(Exception::class, $event->carrier);
        $inputs['trace'] = json_encode($inputs['trace']);
        Exception::query()
            ->create($inputs);
    }
}
