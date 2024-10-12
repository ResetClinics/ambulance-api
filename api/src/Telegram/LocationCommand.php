<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

class LocationCommand implements CommandInterface
{

    public function execute(BotApi $api, Update $update): void
    {

        //$update->getMessage()->getContact()->getPhoneNumber();
        $text = 'бала бала бла бла';
        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
        );
    }

    public function isApplicable(Update $update): bool
    {
       if (!$update->getMessage()?->getContact()) {
           return false;
       }

        return true;
    }
}
