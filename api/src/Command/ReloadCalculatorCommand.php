<?php

declare(strict_types=1);

namespace App\Command;

use App\Flusher;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Services\Payroll\ServiceCalculator\CoddingPayrollCalculatorProcessor;
use App\Services\Payroll\ServiceCalculator\HospitalizationPayrollCalculatorProcessor;
use App\Services\Payroll\ServiceCalculator\SewingPayrollCalculatorProcessor;
use App\Services\Payroll\ServiceCalculator\TherapyPayrollCalculatorProcessor;
use App\Services\Payroll\ServiceCalculator\TransportationPayrollCalculatorProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'reload-calculator',
    description: 'Add a short description for your command',
)]
class ReloadCalculatorCommand extends Command
{
    public function __construct(
        private readonly PayrollCalculatorRepository $calculators,
        private readonly Flusher $flusher,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $calculators = $this->calculators->findAll();
        foreach ($calculators as $calculator) {
            if ($calculator->getTarget() === 'call') {
                $calculator->setProcessor('call_default_calculator');
            } elseif ($calculator->getTarget() === 'payroll') {
                $calculator->setProcessor('payroll_default_calculator');
            } elseif ($calculator->getTarget() === 'shift') {
                $calculator->setProcessor('shift_default_calculator');
            } elseif ($calculator->getTarget() === 'service') {
                $newProcessor  = $this->getProcessor($calculator);
                $calculator->setProcessor($newProcessor);
            }
        }

        $this->flusher->flush();

        return Command::SUCCESS;
    }

    private function getProcessor(mixed $calculator): string
    {
        $processors = [
            TherapyPayrollCalculatorProcessor::class => 'service_therapy_calculator',
            CoddingPayrollCalculatorProcessor::class => 'service_codding_calculator',
            TransportationPayrollCalculatorProcessor::class => 'service_transportation_calculator',
            SewingPayrollCalculatorProcessor::class => 'service_sewing_calculator',
            HospitalizationPayrollCalculatorProcessor::class => 'service_hospitalization_calculator',
        ];

        if (isset($processors[$calculator->getProcessor()])) {
            return $processors[$calculator->getProcessor()];
        }

        return $calculator->getProcessor();
    }
}
