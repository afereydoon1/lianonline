<?php


namespace App\Services\Notification;


interface NotifierInterface
{
    public function send($message, $receiver, $options = []);
}
