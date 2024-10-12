<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

class LocationCommand implements CommandInterface
{

    public function execute(BotApi $api, Update $update): void
    {
        $text = 'бала бала бла бла ' . $update->getMessage()->getContact()->getPhoneNumber();;

        $buttons = [];

        $buttons[] = [
            'text' => 'Локация',
            'request_location' => true
        ];


        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
            false,
            null,
            new ReplyKeyboardMarkup([$buttons])
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
