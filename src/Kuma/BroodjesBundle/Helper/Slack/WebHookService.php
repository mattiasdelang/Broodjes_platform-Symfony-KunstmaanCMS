<?php

namespace Kuma\BroodjesBundle\Helper\Slack;

class WebHookService
{
    private $token;
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function sendCurl($room, $message)
    {
        $data = 'payload=' . json_encode([
                'channel' => "{$room}",
                'text' => $message,
            ]);

        // You can get your webhook endpoint from your Slack settings
        $ch = curl_init('https://hooks.slack.com/services/' . $this->token);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
