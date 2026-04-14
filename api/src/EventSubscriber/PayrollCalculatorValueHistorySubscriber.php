<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\Payroll\PayrollCalculator;
use App\Entity\Payroll\PayrollCalculatorValueHistory;
use DateTimeImmutable;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\UnitOfWork;

class PayrollCalculatorValueHistorySubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $args->getObjectManager();
        $unitOfWork = $entityManager->getUnitOfWork();

        foreach ($unitOfWork->getScheduledEntityInsertions() as $entity) {
            if (!$entity instanceof PayrollCalculator) {
                continue;
            }

            $this->createHistoryRecord(
                $entityManager,
                $unitOfWork,
                $entity,
                $entity->getValue(),
                new DateTimeImmutable('2015-01-01')
            );
        }

        foreach ($unitOfWork->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof PayrollCalculator) {
                continue;
            }

            $changeSet = $unitOfWork->getEntityChangeSet($entity);
            if (!isset($changeSet['value'])) {
                continue;
            }

            $oldValue = $changeSet['value'][0];
            $newValue = $changeSet['value'][1];

            if (!$this->hasHistory($entity)) {
                $this->createHistoryRecord(
                    $entityManager,
                    $unitOfWork,
                    $entity,
                    is_string($oldValue) ? $oldValue : null,
                    new DateTimeImmutable('2015-01-01')
                );
            }

            $this->createHistoryRecord(
                $entityManager,
                $unitOfWork,
                $entity,
                is_string($newValue) ? $newValue : null,
                new DateTimeImmutable('today')
            );
        }
    }

    private function hasHistory(PayrollCalculator $calculator): bool
    {
        return $calculator->getValueHistories()->count() > 0;
    }

    private function createHistoryRecord(
        \Doctrine\ORM\EntityManagerInterface $entityManager,
        UnitOfWork $unitOfWork,
        PayrollCalculator $calculator,
        ?string $value,
        DateTimeImmutable $effectiveFrom
    ): void {
        $history = new PayrollCalculatorValueHistory();
        $history
            ->setCalculator($calculator)
            ->setValue($value)
            ->setEffectiveFrom($effectiveFrom->setTime(0, 0));

        $entityManager->persist($history);
        $unitOfWork->computeChangeSet(
            $entityManager->getClassMetadata(PayrollCalculatorValueHistory::class),
            $history
        );
    }
}
