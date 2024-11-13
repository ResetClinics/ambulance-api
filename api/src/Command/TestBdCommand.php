<?php

namespace App\Command;

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
        private readonly ManagerRegistry $doctrine
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

        $connection = $this->doctrine->getConnection('asterisk');

        $sql = 'SELECT * FROM channels';
        /** @var Statement $statement */
        $statement = $connection->prepare($sql);

        $result = $statement
            ->executeQuery()
            ->fetchAllAssociative();

        dd($result);


        $io->success('Success.');

        return Command::SUCCESS;
    }
}
