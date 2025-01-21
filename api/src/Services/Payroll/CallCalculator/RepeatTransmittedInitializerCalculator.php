<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;

class RepeatTransmittedInitializerCalculator extends AbstractCallCalculator
{
    protected function process(
        Calling $call,
        PayrollCalculator $payrollCalculator,
        Money $accrued,
    ): void {
        $admin = $call->getAdmin();
        $adminInitializer = $call->getOwner()?->getAdmin();

        if ($adminInitializer->getId() !== $admin->getId()){
            $this->createPayrollForEmployee($call, $accrued, $adminInitializer, $payrollCalculator);
        }

        $doctor = $call->getDoctor();
        $doctorInitializer = $call->getOwner()?->getDoctor();

        if ($doctorInitializer->getId() !== $doctor->getId()){
            $this->createPayrollForEmployee($call, $accrued, $doctorInitializer, $payrollCalculator);
        }
    }
}
