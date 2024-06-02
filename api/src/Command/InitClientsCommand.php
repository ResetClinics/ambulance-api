<?php

namespace App\Command;

use App\Entity\Client;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\ClientRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'init:clients',
    description: 'Add a short description for your command',
)]
class InitClientsCommand extends Command
{
    public function __construct(
        private readonly CallingRepository $callings,
        private readonly  ClientRepository $clients,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $callings = $this->callings->findAllByClientNull();
        if (count($callings) === 0){
            $io->success('Больше нет вызовов для инициализации.');
        }

        foreach ($callings as $calling){
            $client = $this->clients->findByPhone($calling->getPhone());

            if (!$client){
                $client = new Client(
                    $calling->getPhone(),
                    $calling->getName()
                );
                $this->clients->save($client);
            }

            $calling->setClient($client);
            $this->flusher->flush();
        }

        $io->success('Клиенты инициализированы.');

        return Command::SUCCESS;
    }
}
