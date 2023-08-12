<?php

namespace Winata\Core\Response\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnErrorEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $performerBy = 'system';
    /**
     * Create a new event instance.
     */
    public function __construct(
        public array|object $carrier,
    )
    {
        /*$performer = fluent(config('winata.response.performer'));
        if ($performer->from == 'auth' && \auth()->check()){
            $user = \auth()->user();
            $performedBy = "";
            foreach ($performer->columns as $index => $column){
                if ((count($performer->columns) -1 ) != $index ){
                    $performedBy .= "{$user->$column} {$performer->separator}";
                }else{
                    $performedBy .= $user->$column;
                }
            }
            $this->performerBy = $performedBy;
        }*/
    }
}
