<?php

namespace App\Services;

use App\Entity\Calling\Calling;
use App\Repository\DeviceRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CallingSender
{
    private HttpClientInterface $client;
    private DeviceRepository $devices;

    public function __construct(
        HttpClientInterface $client,
        DeviceRepository $devices
    )
    {
        $this->client = $client;
        $this->devices = $devices;
    }

    public function sendToAdmin(Calling $calling, string $title, string $body): void
    {
        $devices = $this->devices->findBy(['user' => $calling->getAdmin()]);

        foreach ($devices as $device){
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