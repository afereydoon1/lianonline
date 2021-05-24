<?php


namespace App\Services\Notification;


class Sms implements NotifierInterface
{

    protected $sender;

    public function __construct($sender)
    {
        $this->sender = $sender;
    }

    public function send($message, $receiver, $options = []) {
        return $this->sender->send($message, $receiver);
    }

    public function sendOtpCode($code, $receiver, $options = []) {
        return $this->sender->sendOtpCode($code, $receiver);
    }
}
