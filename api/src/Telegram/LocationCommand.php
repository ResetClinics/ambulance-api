<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

class LocationCommand implements CommandInterface
{

    public function execute(BotApi $api, Update $update): void
    {

        $text = 'бала бала';
        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
        );
    }

    public function isApplicable(Update $update): bool
    {
        if (!$update->getMessage()) {
            return false;
        }

        return true;
    }
}
