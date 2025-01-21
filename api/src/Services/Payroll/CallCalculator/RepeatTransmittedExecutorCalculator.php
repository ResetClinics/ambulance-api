<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;

class RepeatTransmittedExecutorCalculator extends AbstractCallCalculator
{
    protected function process(
        Calling $call,
        PayrollCalculator $payrollCalculator,
        Money $accrued,
    ): void {

        $admin = $call->getAdmin();

        if ($call->getOwner()?->getAdmin()->getId() !== $admin->getId()){
            $this->createPayrollForEmployee($call, $accrued, $admin, $payrollCalculator);
        }

        $doctor = $call->getDoctor();

        if ($call->getOwner()?->getDoctor()->getId() !== $doctor->getId()){
            $this->createPayrollForEmployee($call, $accrued, $doctor, $payrollCalculator);
        }
    }
}
