<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use TelegramBot\Api\Types\Update;

class GetContactCommand implements CommandInterface
{

    public function execute(BotApi $api, Update $update): void
    {
        $text = 'Получен телефон ' . $update->getMessage()->getContact()->getPhoneNumber();;

        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
            false,
            null,
            new ReplyKeyboardRemove()
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
