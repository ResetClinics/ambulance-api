<?php

namespace App\Command;

use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'firebase:send-message',
    description: 'Проверка отправки push',
)]
class FirebaseSendMessageCommand extends Command
{
    public function __construct(
        private readonly Messaging $messaging
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //todo переделать через сервис отправки пользователю
        $io = new SymfonyStyle($input, $output);
        $message = CloudMessage::fromArray([
            'token' => '',
            'notification' => [
                'title' => 'Заголовок уведомления',
                'body' => 'Текст уведомления',
            ],
            'data' => [],
        ]);

        $this->messaging->send($message);

        $io->success('Message sent!');

        return Command::SUCCESS;
    }
}
