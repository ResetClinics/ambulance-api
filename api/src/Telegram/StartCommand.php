<?php

namespace App\Telegram;

use App\Repository\TgChatRepository;
use BoShurik\TelegramBotBundle\Telegram\Command\AbstractCommand;
use BoShurik\TelegramBotBundle\Telegram\Command\PublicCommandInterface;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardRemove;
use TelegramBot\Api\Types\Update;

class StartCommand extends AbstractCommand implements PublicCommandInterface
{

    public function __construct(
        private readonly TgChatRepository $tgChatRepository
    )
    {
    }

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

        $chatId = $update->getMessage()->getChat()->getId();

        $chat = $this->tgChatRepository->findOneByChatId((string)$chatId);

        if (!$chat) {
            $buttons = [[
                'text' => '❗Отправить контакт❗',
                'request_contact' => true,
                'one_time_keyboard' => true,
            ]];

            $text = "Привет!\n
Я виртуальный помощник клиники Ресет\r\n
Можешь в этом убедиться и позвонить Андрею Седову\r\n
Сейчас мы подключим твой телеграмм к рассылке уведомлений о рабочих сменах (вместо СМС)\r\n
Чтобы начать нажми на кнопку «Отправить контакт»\r\n
🔻Она сейчас внизу экрана🔻";

            $api->sendMessage(
                $update->getMessage()->getChat()->getId(),
                $text,
                'markdown',
                false,
                null,
                new ReplyKeyboardMarkup([$buttons])
            );
        }else {

            $user = $chat->getUser();
            if (!$user || !$user->isActive()) {
                $message = $chat->getUser()->getName() . ', ваш пользователь деактивирован. Свяжитесь с администратором';
            }else {
                $message = 'Привет, ' . $chat->getUser()->getName() . ', сюда вам будут приходить оповещения';
            }

            $api->sendMessage(
                $update->getMessage()->getChat()->getId(),
                $message,
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );
        }
    }
}
