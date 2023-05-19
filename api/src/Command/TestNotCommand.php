<?php

namespace App\Command;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\LeadModel;
use App\Services\AmoCRM;
use App\Services\CallingSender;
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
    private AmoCRMApiClient $client;
    private CallingSender $sender;


    public function __construct(
        AmoCRM        $amoCRM,
        CallingSender $sender
    )
    {
        parent::__construct();

        $this->client = $amoCRM->getClient();
        $this->sender = $sender;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
//20680455


        $lead = $this->client->leads()->getOne(20680455);

        if (!$lead){
            throw new NotFoundHttpException('Не получен лид');
        }

        $leadContacts = $lead->getContacts();

        dd($leadContacts);


        //$contact = $this->client->contacts()->getOne($link->getContacts()[0]->getId());

        dump($lead);
        dd($lead->getLink());

        $newLead = new LeadModel();
        $newLead->setName($lead->getName())
            ->setCreatedBy(0)
            ->setStatusId(38307805)
            ->setPipelineId(4018768)
            ->setResponsibleUserId($lead->getResponsibleUserId())
            ->setCustomFieldsValues($lead->getCustomFieldsValues())
            ->setContacts(
                (new ContactsCollection())->add($lead->getMainContact())
            );

        $leadsCollection = new LeadsCollection();
        $leadsCollection->add($newLead);

        $ddd = $this->client->leads()->add($leadsCollection);

        dump($ddd);

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
