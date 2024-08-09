<?php

namespace App\State;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Hospital\Hospital;
use App\Services\Hospital\PartnerReward;
use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Security;

class HospitalProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private readonly ProcessorInterface $removeProcessor,
        private readonly Security $security,
        private readonly PartnerReward $partnerReward,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            return;
        }
        /** @var Hospital $data */
        if ($data->getStatus() === 'inpatient' && $data->getHospitalizedAt() === null){
            $data->setHospitalizedAt(new DateTimeImmutable());
            $data->setHospitalizedBy($this->security->getUser());
            $data->setPartnerReward(0);
        }

        if ($data->getStatus() === 'completed' && $data->getDischargedAt() === null){
            $data->setDischargedAt(new DateTimeImmutable());
            $data->setDischargedBy($this->security->getUser());
            $this->partnerReward->calculate($data);
        }

        $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
