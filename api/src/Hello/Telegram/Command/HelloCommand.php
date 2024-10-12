<?php


namespace App\Hello\Telegram\Command;

use BoShurik\TelegramBotBundle\Telegram\Command\AbstractCommand;
use BoShurik\TelegramBotBundle\Telegram\Command\PublicCommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\Update;

class HelloCommand extends AbstractCommand implements PublicCommandInterface
{
    public function getName(): string
    {
        return '/hello';
    }

    public function getDescription(): string
    {
        return 'Example command';
    }

    public function execute(BotApi $api, Update $update): void
    {
        preg_match(self::REGEXP, $update->getMessage()->getText(), $matches);
        $who = !empty($matches[3]) ? $matches[3] : 'World';

        $buttons = [];

        $buttons[] = [
            'text' => 'Контакт',
            'request_contact' => true,
            'callback_data' => 'contact'
        ];

        $text = sprintf('Hello *%s*', $who);
        $api->sendMessage(
            $update->getMessage()->getChat()->getId(),
            $text,
            'markdown',
            false,
            null,
            new ReplyKeyboardMarkup([$buttons])
        );
    }


    public function handleCallbackQuery(BotApi $api, Update $update): void
    {
        $callbackQuery = $update->getCallbackQuery();
        if ($callbackQuery->getData() === 'contact') {
            $chatId = $callbackQuery->getMessage()->getChat()->getId();
            $api->sendContact($chatId, 'John Doe', '+1234567890');
        }
    }

    protected function configureCallbackQueryHandlers(BotApi $api): array
    {
        return [
            'contact' => [$this, 'handleCallbackQuery'],
        ];
    }
}
