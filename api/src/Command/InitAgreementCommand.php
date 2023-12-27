<?php

namespace App\Command;

use App\Flusher;
use App\Repository\Partner\Agreement\AgreementRepository;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use App\Repository\Partner\Agreement\RowRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'init:agreement',
    description: 'Add a short description for your command',
)]
class InitAgreementCommand extends Command
{


    public function __construct(
        private readonly RowRepository $agreementRows,
        private readonly AgreementRepository $agreements,
        private readonly AgreementTemplateRepository $templates,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

       // foreach ($this->agreementRows as $agreementRow){
       //     $this->agreementRows->remove($agreementRow);
       // }
//

        foreach ($this->agreements->findAll() as $agreement){
            $this->agreements->remove($agreement);
        }

        $this->flusher->flush();



        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
