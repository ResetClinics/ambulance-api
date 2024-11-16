<?php

namespace App\Asterisk\UseCase\Channel\DeleteByClientPhone;

use App\Asterisk\Repository\ChannelRepository;

readonly class Handler
{
    public function __construct(
        private ChannelRepository $channels,
    )
    {
    }

    public function handle(Command $command): void
    {
        //TODO надо вынести валидации в модель Phone
        $clientPhone = preg_replace('/[^0-9]/', '', $command->clientPhone);
        $this->channels->deleteChannelByClientPhoneNumber($clientPhone);
    }
}