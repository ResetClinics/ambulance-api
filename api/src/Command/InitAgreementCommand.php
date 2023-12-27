<?php

namespace App\Command;

use App\Entity\Partner;
use App\Flusher;
use App\Repository\Partner\Agreement\AgreementRepository;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use App\Repository\Partner\Agreement\RowRepository;
use App\Repository\PartnerRepository;
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
        private readonly PartnerRepository $partners,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->agreementRows->findAll() as $agreementRow){
            $this->agreementRows->remove($agreementRow, true);
        }

        foreach ($this->agreements->findAll() as $agreement){
            $this->agreements->remove($agreement, true);
        }

        $this->flusher->flush();

        /** @var Partner $partner */
        foreach ($this->partners->findAll() as $partner){
            $io->success($partner->getName());
            $this->createAgreement($partner);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }

    private function createAgreement(Partner $partner)
    {
        $agreement = new Partner\Agreement\Agreement();
        $agreement->setPartner($partner);
        $agreement->setStartsAt(new \DateTimeImmutable('01.12.2023'));
        /** @var Partner\Agreement\AgreementTemplate $template */
        foreach ($this->templates as $template){
            $row = new Partner\Agreement\Row();
            $row->setAgreement($agreement);
            $row->setService($template->getService());
            $row->setDistance($template->getDistance());
            $row->setPercent($template->getPercent());
            $row->setRepeatNumber($template->getRepeatNumber());

            $agreement->addRow($row);
        }
        $this->agreements->save($agreement, true);
    }
}
