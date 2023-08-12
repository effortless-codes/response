<?php

namespace Winata\Core\Response\Listeners\OnErrorEvent;

use Winata\Core\Telegram\Concerns\Messages\Message;

class SendToTelegram
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
        $sendToTelegram = sendToTelegram(
            token: config('winata.response.driver.telegram.token'),
            chatId: config('winata.response.driver.telegram.chat_id')
        )
            ->setTitle(title: config('winata.response.driver.telegram.formatting.title'), callable: function (Message $message) {
                return $message
                    ->setMessage(message: "FROM APP : " . config('winata.response.app_name'));
            });

        /* begin: cc */
        $sendToTelegram
            ->setCc(callable: function (Message $message) use ($event){

                $performer = $event->performerBy;
                if ($user = auth()->user()){
                    $who = config('winata.response.performer.performer_column');
                    $performer = $user->$who;
                }

                return $message
                    ->setMessage("performerBy: {$performer}")
                    ->setMessage(config('winata.response.driver.telegram.formatting.cc'));
            });
        /* end: cc */

        $data = array_to_object($event->carrier);

        if (isset($data->rc)) {
            $sendToTelegram
                ->setMessage(message: "RC : {$data->rc}");
        }

        $now = now()->toDateTimeString();
        $sendToTelegram
            ->setMessage(message: "MESSAGE : {$data->message}")
            ->setMessage(message: "TIME : {$now}")
            ->setMessage(message: "CODE : {$data->code}")
            ->setMessage(message: "URL : {$data->url}");
        if (isset($data->data)) {
            $sendToTelegram
                ->setMessage(message: "DATA : " . json_encode($data->data));
        }

        /* begin:: additional */
        $sendToTelegram
            ->setMessage(callable: function (Message $message) use ($data) {
                return $message
                    ->setMessage(message: '')
                    ->setMessage(message: '----ADDITIONAL----', format: '*')
                    ->setMessage(message: "SOURCE : {$data->source}")
                    ->setMessage(message: "FILE : {$data->file}")
                    ->setMessage(message: "LINE : {$data->line}");
            });
        /* end:: additional */

        $sendToTelegram
            ->sendMessage();
    }
}
