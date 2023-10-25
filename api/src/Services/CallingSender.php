<?php

namespace App\Services;

use App\Entity\Calling\Calling;
use App\Repository\DeviceRepository;
use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallingSender
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly DeviceRepository $devices,
        private readonly string $token
    )
    {
    }

    public function sendToAdmin(Calling $calling, string $title, string $body): void
    {
        $devices = $this->devices->findBy(['user' => $calling->getAdmin()]);

        foreach ($devices as $device){

            try {
                $this->client->request('POST', 'https://fcm.googleapis.com/fcm/send', [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Authorization' => 'key=' . $this->token
                    ],
                    'json' => [
                        "data" =>  [
                            "callingId" =>  $calling->getId(),
                            "callingStatus" =>  $calling->getStatus(),
                            "url" =>  'сalls',
                        ],
                        'to' =>  $device->getId(),
                        'notification' => [
                            'title' => $title,
                            'body' =>  $body,
                        ]

                    ],
                ]);
            }catch (Exception $e){}

            $this->client->request('POST', 'https://exp.host/--/api/v2/push/send', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    "data" =>  [
                        "callingId" =>  $calling->getId(),
                        "callingStatus" =>  $calling->getStatus(),
                        "url" =>  'сalls',
                    ],
                    'to' => $device->getId(),
                    'title' => $title,
                    'body' =>  $body,
                ],
            ]);
        }
    }

}