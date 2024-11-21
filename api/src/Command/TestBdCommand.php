<?php

namespace App\Command;

use App\Asterisk\UseCase\Channel\AddOrUpdate\Handler;
use App\Services\ATS\BlacklistService\McnBlacklistService;
use Doctrine\DBAL\Exception;
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
        private readonly McnBlacklistService $blacklists,
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


        //$this->blacklists->addToBlacklist('79657965566');
        //$this->blacklists->deleteFromBlacklist('79657965566');


        $io->success('Success.');

        return Command::SUCCESS;
    }


}
