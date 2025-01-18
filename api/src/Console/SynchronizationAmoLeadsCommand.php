<?php

declare(strict_types=1);

namespace App\Console;

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\BaseEnumCodeCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use App\Entity\Calling\Calling;
use App\Entity\Partner;
use App\Flusher;
use App\Repository\CallingRepository;
use App\Repository\PartnerRepository;
use App\Services\AmoCRM;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'syn:amo:leads',
    description: 'Синхронизация лидов в системе с лидами amo',
)]
class SynchronizationAmoLeadsCommand extends Command
{
    private AmoCRMApiClient $client;

    public function __construct(
        AmoCRM $amoCRM,
        private readonly CallingRepository $callings,
        private readonly PartnerRepository $partners,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
        $this->client = $amoCRM->getClient();
    }

    /**
     * @throws NonUniqueResultException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->callings->findAll() as $calling) {
            $this->updateCalling($calling);
            // dd(1);
        }

        $io->success('Synchronization completed.');

        return Command::SUCCESS;
    }

    /**
     * @throws NonUniqueResultException
     */
    private function updateCalling(Calling $calling): void
    {
        if ($calling->isDeleted()) {
            return;
        }
        try {
            $lead = $this->client->leads()->getOne(
                $calling->getNumberCalling(),
                [LeadModel::CONTACTS, LeadModel::CATALOG_ELEMENTS]
            );

            if (!$lead) {
                dump($calling->getNumberCalling() . ' *********************');
                return;
            }
            foreach ($lead->getCustomFieldsValues() as $field) {
                $this->updateCallingPartner($calling, $field);
            }
        } catch (AmoCRMApiException $exception) {
            if ($exception->getCode() === 204) {
                $calling->setDeleted(true);
                $this->flusher->flush();
                return;
            }
        }
    }

    /**
     * @throws NonUniqueResultException
     */
    private function updateCallingPartner(Calling $calling, BaseCustomFieldValuesModel $field): void
    {
        if ($field->getFieldId() !== 882361) {
            return;
        }
        $first = $field->getValues()?->first();
        if (!$first) {
            return;
        }
        if (!$first instanceof BaseEnumCodeCustomFieldValueModel) {
            return;
        }
        if (!$first->getEnumId()) {
            return;
        }
        $partnerExternalId = (string)$first->getEnumId();
        $partnerName = $first->getValue();
        if ($calling->getPartnerName() !== $partnerName) {
            dump($calling->getNumberCalling() . ' : ' . $calling->getPartnerName() . ' - ' . $partnerName);
        }

        $partner = $this->partners->findOneByExternalId($partnerExternalId);
        if (!$partner) {
            $partner = new Partner();
            $partner->setExternalId($partnerExternalId);
            $this->partners->save($partner);
        }
        $partner->setName($partnerName);
        $calling->setPartner($partner);
        $calling->setPartnerName($partnerName);

        $this->flusher->flush();
    }
}
