<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\Calling\Status;
use App\Repository\Payroll\ServicePayrollRepository;
use App\Services\Payroll\Processor\CallPayrollCalculatorProcessorInterface;
use App\Services\Payroll\Processor\CoddingPayrollCalculatorProcessor;
use App\Services\Payroll\Processor\HospitalizationPayrollCalculatorProcessor;
use App\Services\Payroll\Processor\SewingPayrollCalculatorProcessor;
use App\Services\Payroll\Processor\TherapyPayrollCalculatorProcessor;
use App\Services\Payroll\Processor\TransportationPayrollCalculatorProcessor;
use Exception;

readonly class CallPayrollCalculator
{
    private array $processors;

    public function __construct(
        private TherapyPayrollCalculatorProcessor $therapyPayrollCalculatorProcessor,
        private CoddingPayrollCalculatorProcessor $coddingPayrollCalculatorProcessor,
        private TransportationPayrollCalculatorProcessor $transportationPayrollCalculatorProcessor,
        private SewingPayrollCalculatorProcessor $sewingPayrollCalculatorProcessor,
        private HospitalizationPayrollCalculatorProcessor $hospitalizationPayrollCalculatorProcessor,
        private ServicePayrollRepository $servicePayrolls,
    ) {
        $this->processors = [
            'service_therapy_calculator' => $this->therapyPayrollCalculatorProcessor,
            'service_codding_calculator' => $this->coddingPayrollCalculatorProcessor,
            'service_transportation_calculator' => $this->transportationPayrollCalculatorProcessor,
            'service_sewing_calculator' => $this->sewingPayrollCalculatorProcessor,
            'service_hospitalization_calculator' => $this->hospitalizationPayrollCalculatorProcessor,
        ];
    }

    public function calculate(Calling $call): void
    {
        if ($call->getStatus() !== Status::COMPLETED) {
            return;
        }

        foreach ($call->getServices() as $callService) {
            /** @var string|null $calculator */
            $calculator = $callService->getService()?->getEmployeePayrollCalculator()?->getProcessor();
            if ($calculator === null) {
                continue;
            }

            /** @var mixed $value */
            $value = $callService->getService()?->getEmployeePayrollCalculator()?->getValue();

            // TODO убрать в калькулятор
            $this->servicePayrolls->removeByCallServiceId($callService->getService()->getId());

            $processor = $this->getProcessor($calculator);

            $processor->calculate($callService, $value);
        }
    }

    private function getProcessor($processor): CallPayrollCalculatorProcessorInterface
    {
        if (!isset($this->processors[$processor])) {
            throw new Exception('Unknown processor');
        }

        return $this->processors[$processor];
    }
}
