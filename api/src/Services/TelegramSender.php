<?php

namespace App\Services;

use App\Entity\User\User;
use App\Repository\TgChatRepository;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

readonly class TelegramSender
{

    public function __construct(
        private BotApi           $botApi,
        private TgChatRepository $tgChatRepository
    )
    {
    }

    public function send(User $user, string $message): void
    {
        $chats = $this->tgChatRepository->findByUser($user);
        foreach ($chats as $chat) {
            $this->botApi->sendMessage(
                (int)$chat->getChatId(),
                $message,
                'markdown',
                false,
                null,
                new ReplyKeyboardRemove()
            );
        }
    }
}