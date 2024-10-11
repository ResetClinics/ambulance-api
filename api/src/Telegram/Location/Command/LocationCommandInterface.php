<?php


namespace App\Telegram\Location\Command;

use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Update;

interface LocationCommandInterface
{
    public function getId(): string;

    public function locationExecute(BotApi $api, Update $update): void;
}
