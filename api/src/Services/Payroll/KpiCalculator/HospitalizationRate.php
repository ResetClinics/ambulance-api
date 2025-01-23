<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;

final readonly class HospitalizationRate extends AbstractKpiProcessor
{
    protected function getKPI(KpiRecord $kpiRecord): float
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $countCalls = 0;
        $countStationary = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            foreach ($call->getServices() as $callService) {
                if (
                    $callService->isStationary() &&
                    ($callService->getClinic()?->getId() === 1 || $callService->getClinic()?->getId() === 2)
                ) {
                    ++$countStationary;
                }
            }
            ++$countCalls;
        }

        return (float)($countStationary > 0 ? $countCalls / $countStationary : 100);
    }

    protected function getRates(PayrollCalculator $calculator): array
    {
        return [
            ['min' => 1, 'max' => 5, 'rate' => 1.3],
            ['min' => 5, 'max' => 6, 'rate' => 1],
            ['min' => 6, 'max' => 7, 'rate' => 0.7],
            ['min' => 7, 'max' => 100, 'rate' => 0],
        ];
    }
}
