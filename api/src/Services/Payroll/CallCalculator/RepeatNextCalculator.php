<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use App\Entity\Calling\Calling;
use App\Entity\Money\Money;
use App\Entity\Payroll\PayrollCalculator;

class RepeatNextCalculator extends AbstractCallCalculator
{
    protected function process(
        Calling $call,
        PayrollCalculator $payrollCalculator,
        Money $accrued,
    ): void {

        if ($call->getCountRepeat() < 2){
            return;
        }

        $admin = $call->getAdmin();

        $this->createPayrollForEmployee($call, $accrued, $admin, $payrollCalculator);

        $doctor = $call->getDoctor();

        $this->createPayrollForEmployee($call, $accrued, $doctor, $payrollCalculator);
    }
}
