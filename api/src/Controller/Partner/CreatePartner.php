<?php

namespace App\Controller\Partner;

use App\Entity\Partner;
use App\Flusher;
use App\Repository\Partner\Agreement\AgreementRepository;
use App\Repository\Partner\Agreement\AgreementTemplateRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CreatePartner extends AbstractController
{
    public function __construct(
        private readonly AgreementTemplateRepository $templates,
        private readonly AgreementRepository $agreements,
        private readonly Flusher $flusher,
    ) {}

    public function __invoke(Partner $partner): Partner
    {
        $agreement = new Partner\Agreement\Agreement();
        $agreement->setPartner($partner);
        $agreement->setStartsAt(new DateTimeImmutable('01.12.2023'));
        foreach ($this->templates->findAll() as $template) {
            $row = new Partner\Agreement\Row();
            $row->setAgreement($agreement);
            $row->setService($template->getService());
            $row->setDistance($template->getDistance());
            $row->setPercent($template->getPercent());
            $row->setRepeatNumber($template->getRepeatNumber());

            $agreement->addRow($row);
        }
        $this->agreements->save($agreement);

        $this->flusher->flush();

        return $partner;
    }
}