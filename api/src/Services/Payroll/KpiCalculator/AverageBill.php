<?php

declare(strict_types=1);

namespace App\Services\Payroll\KpiCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\Payroll\PayrollCalculator;

final readonly class AverageBill extends AbstractKpiProcessor
{
    protected function getKPI(KpiRecord $kpiRecord): float
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
            ++$count;
        }

        return (float)($count > 0 ? $price / $count : 0);
    }
}
