<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use DateTime;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use TelegramBot\Api\Types\Update;

readonly class GetAudioCommand implements CommandInterface
{
    public function execute(BotApi $api, Update $update): void
    {
        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            'Ваше сообщение получено, ожидайте',
            'markdown',
            false,
            null,
            new ReplyKeyboardRemove()
        );
    }

    public function isApplicable(Update $update): bool
    {

        $timestamp = (new DateTime())->format('YmdHis');
        $filePath = dirname(__DIR__) . "/../var/{$timestamp}.json";
        file_put_contents(
            $filePath,
            $update->toJson(),
            //json_encode($update->toJson()),
            FILE_APPEND);


       if (!$update->getMessage()?->getAudio()) {
           return false;
       }

        return true;
    }
}
