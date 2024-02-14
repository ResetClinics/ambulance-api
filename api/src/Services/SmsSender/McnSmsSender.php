<?php

namespace App\Services\SmsSender;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class McnSmsSender
{
    public function __construct(
        readonly private HttpClientInterface $client,
    )
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $phone, string $message): void
    {
        $this->client->request(
            'POST',
            'https://a2p-sms-api.mcn.ru/api/a2p_sms/api/v1.1/send_sms',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                ],
                'json' => [
                    "title" => 'MCNtelecom',
                    'sender' => "MCNtelecom",
                    'receiver' => $phone,
                    'msgdata' =>  $message,
                ],
            ]
        );
    }
}
