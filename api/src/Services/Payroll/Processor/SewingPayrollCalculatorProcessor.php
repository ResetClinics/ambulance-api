<?php

declare(strict_types=1);

namespace App\Services\Payroll\Processor;

use App\Entity\Calling\Row;
use App\Entity\Money\Money;
use App\Entity\Payroll\ServicePayroll;
use App\Entity\User\User;
use App\Repository\Payroll\ServicePayrollRepository;

readonly class SewingPayrollCalculatorProcessor implements CallPayrollCalculatorProcessorInterface
{
    public function __construct(
        private ServicePayrollRepository $servicePayrolls,
    ) {}

    public function calculate(Row $callService, mixed $value): void
    {
        $admin = $callService->getCalling()->getAdmin();

        $this->createPayrollForEmployee($callService, $value, $admin);

        $doctor = $callService->getCalling()->getDoctor();

        $this->createPayrollForEmployee($callService, $value, $doctor);
    }

    public function createPayrollForEmployee(Row $callService, mixed $value, ?User $admin): void
    {
        $accrued = new Money(
            (int)(($callService->getPrice() * $value) * 100)
        );

        $payroll = new ServicePayroll();
        $payroll->setAccruedAt($callService->getCalling()->getCompletedAt());
        $payroll->setCallService($callService);
        $payroll->setEmployee($admin);
        $payroll->setAccrued($accrued);

        $this->servicePayrolls->add($payroll);
    }
}
