<?php

declare(strict_types=1);

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\ReplyKeyboardRemove;

#[AsCommand(
    name: 'lead:test',
    description: 'Init user for exchange',
)]
class TestCommand extends Command
{

    public function __construct(
        private readonly BotApi $botApi
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->botApi->sendMessage(
            927480477,
            'тест',
            'markdown',
            false,
            null,
            new ReplyKeyboardRemove()
        );

        $io->success('.');

        return Command::SUCCESS;
    }
}
