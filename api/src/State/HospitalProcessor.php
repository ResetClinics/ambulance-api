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

readonly class HospitalProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        #[Autowire(service: 'api_platform.doctrine.orm.state.remove_processor')]
        private ProcessorInterface $removeProcessor,
        private Security           $security,
        private PartnerReward      $partnerReward,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($data, $operation, $uriVariables, $context);
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
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

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
