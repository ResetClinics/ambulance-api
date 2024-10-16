<?php

namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\CommandInterface;
use DateTime;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

readonly class GetFileCommand implements CommandInterface
{
    public function __construct(
    )
    {
    }

    public function execute(BotApi $api, Update $update): void
    {
    }

    public function isApplicable(Update $update): bool
    {
        $timestamp = (new DateTime())->format('YmdHis');
        $filePath = __DIR__ . "/var/{$timestamp}.json";

        file_put_contents($filePath, json_encode($update->toJson()));

        return true;
    }
}
