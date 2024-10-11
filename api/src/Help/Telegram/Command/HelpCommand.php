<?php

namespace App\Help\Telegram\Command;

use BoShurik\TelegramBotBundle\Telegram\Command\HelpCommand as BaseCommand;
use TelegramBot\Api\Types\Update;

class HelpCommand extends BaseCommand
{
    public function isApplicable(Update $update): bool
    {
        return $update->getMessage() !== null;
    }
}
