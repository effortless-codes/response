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
        $isLogging = config('winata.response.reportable.database');
        if ($isLogging['logging']){
            $inputs = getFillableAttribute(Exception::class, $event->carrier);
            if ($isLogging['store_trace']){
                $inputs['trace'] = json_encode($inputs['trace']);
            }else{
                $inputs['trace'] = null;
            }
            Exception::query()
                ->create($inputs);
        }
    }
}
