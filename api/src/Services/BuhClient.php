<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Calling\Calling;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BuhClient
{
    public function __construct(
        readonly private HttpClientInterface $client,
        readonly private SerializerInterface $serializer,
        readonly private string $buhUsername,
        readonly private string $buhPassword
    ) {}

    /**
     * @throws TransportExceptionInterface
     */
    public function send(Calling $call): void
    {

        $serializedData = $this->serializer->serialize($call, 'json', [
            'groups' => [
                'exchange_calling:read',
                'partner:item:read',
                'user:item:read',
                'service:item:read',
                'client:item:read',
            ],
        ]);

        $response = $this->client->request(
            'POST',
            'http://78.37.191.232:45808/UMC_Union/hs/calls/UploadCalls',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Sec-Fetch-Mode' => 'cors',
                ],
                'auth_basic' => [$this->buhUsername, $this->buhPassword],
                'json' => [
                    $serializedData
                ],
            ]
        );

        dd($response->toArray(false));


    }
}
