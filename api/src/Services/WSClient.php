<?php

namespace App\Services;

use WebSocket\Client;

class WSClient
{
    public function __construct(
        private readonly string $url
    )
    {}

    public function send(string $message): void
    {
        $client = new Client($this->url);

        $client->text($message);

        $client->close();
    }
}