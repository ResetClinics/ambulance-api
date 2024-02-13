<?php

namespace App\Services\SmsSender;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SmsSender
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
            'GET',
            'https://sms.ru/sms/send?api_id=6F4B7B5D-AB04-3F68-8B13-AF80351F271E&to=79657965566&msg=“Дата”%0D%0A
“Время смены” (9.00-22.30)%0D%0A
Бригада “№”%0D%0A
В: “Фамилия врача”%0D%0A
А: “Фамилия администратора”%0D%0A
Ш: “Фамилия шофера” (если он есть в смене)%0D%0A
Авто: “Марка авто” (если указана)&json=1'
        );
    }
}
