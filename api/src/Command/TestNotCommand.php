<?php

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\EntitiesServices\Links;
use AmoCRM\Filters\EntitiesLinksFilter;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Filters\LinksFilter;
use AmoCRM\Helpers\EntityTypesInterface;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\LinkModel;
use App\Services\AmoCRM;
use App\Services\CallingSender;
use App\Services\HospitalizationScheduler;
use App\Services\RepeatedCallScheduler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsCommand(
    name: 'test:repeat',
    description: 'Add a short description for your command',
)]
class TestNotCommand extends Command
{
    private HospitalizationScheduler $scheduler;
    private CallingSender $sender;


    public function __construct(
        HospitalizationScheduler        $scheduler,
        CallingSender $sender
    )
    {
        parent::__construct();

        $this->sender = $sender;
        $this->scheduler = $scheduler;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);


        $this->scheduler->schedule(20680455);


        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
