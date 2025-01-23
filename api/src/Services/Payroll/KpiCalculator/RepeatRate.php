<?php

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;

readonly class RepeatRate extends AbstractKpiProcessor
{
    protected function getKPI(KpiRecord $kpiRecord,): float
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $countCalls = 0;
        $countRepeat = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            foreach ($call->getServices() as $callService) {
                if ($callService->isReplay()) {
                    $countRepeat++;
                }
            }
            $countCalls++;
        }

        return (float)($countRepeat > 0 ? $countCalls / $countRepeat : 100);
    }

    protected function getRates(PayrollCalculator $calculator): array
    {
        return [
            ['min' => 1, 'max' => 2, 'rate' => 1.3],
            ['min' => 2, 'max' => 3, 'rate' => 1],
            ['min' => 3, 'max' => 4, 'rate' => 0.7],
            ['min' => 4, 'max' => 100, 'rate' => 0]
        ];
    }
}