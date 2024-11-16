<?php

namespace App\Command;

use App\Asterisk\UseCase\Channel\AddOrUpdate\Handler;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Statement;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'test:bd',
    description: 'Add a short description for your command',
)]
class TestBdCommand extends Command
{
    public function __construct(
        private readonly Handler $handler,
        private readonly \App\Asterisk\UseCase\Channel\DeleteByClientPhone\Handler $deleteHandler
    )
    {
        parent::__construct();
    }


    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $command = new \App\Asterisk\UseCase\Channel\AddOrUpdate\Command('7777777777', '1111111111');

        $this->handler->handle($command);


        $command = new \App\Asterisk\UseCase\Channel\DeleteByClientPhone\Command('7777777777');

        $this->deleteHandler->handle($command);


        $io->success('Success.');

        return Command::SUCCESS;
    }


}
