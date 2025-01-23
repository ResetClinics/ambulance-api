<?php

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;

readonly final class AverageBill extends AbstractKpiProcessor
{

    protected function getKPI(KpiRecord $kpiRecord,): float
    {
        $calls = $this->calls->findAllCompletedOfTheEmployeeByCompletionDateIncludedInPeriod(
            $kpiRecord->getDocument()->getPeriodStart(),
            $kpiRecord->getDocument()->getPeriodEnd(),
            $kpiRecord->getEmployee()->getId()
        );

        $count = 0;
        $price = 0;

        /** @var Calling $call */
        foreach ($calls as $call) {
            $price += $call->getPrice() ?: 0;
            $count++;
        }

        return (float)($count > 0 ? $price / $count : 0);
    }

    protected function getRates(PayrollCalculator $calculator): array
    {
        return [
            ['min' => 0, 'max' => 20500, 'rate' => 0],
            ['min' => 20500, 'max' => 21500, 'rate' => 0.7],
            ['min' => 21.500, 'max' => 22500, 'rate' => 1],
            ['min' => 22500, 'max' => 100000, 'rate' => 1.3]
        ];
    }
}