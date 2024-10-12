<?php


namespace App\Telegram;

use BoShurik\TelegramBotBundle\Telegram\Command\AbstractCommand;
use BoShurik\TelegramBotBundle\Telegram\Command\PublicCommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

class StartCommand extends AbstractCommand implements PublicCommandInterface
{
    public function getName(): string
    {
        return '/start';
    }

    public function getDescription(): string
    {
        return 'Команда запуска бота';
    }

    public function execute(BotApi $api, Update $update): void
    {

        $buttons = [];

        $buttons[] = [
            'text' => 'Отправить контакт',
            'request_contact' => true,
            'one_time_keyboard' => true,
        ];

        $text = "Для начала работы отправьте пожалуйста свой контакт";
        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
            false,
            null,
            new ReplyKeyboardMarkup([$buttons])
        );
    }
}
