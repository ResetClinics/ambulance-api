<?php

namespace App\Command;

use App\Entity\Payroll\PayrollCalculator;
use App\Flusher;
use App\Repository\Payroll\KPIRepository;
use App\Repository\Payroll\MetricRepository;
use App\Repository\Payroll\PayrollCalculatorRepository;
use App\Repository\Payroll\TransportRepository;
use App\Services\Payroll\Processor\TherapyPayrollCalculatorProcessor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'payroll:reload-config',
    description: 'Add a short description for your command',
)]
class PayrollReloadConfigCommand extends Command
{
    public function __construct(
        private readonly MetricRepository $metrics,
        private readonly KPIRepository $kpis,
        private readonly TransportRepository $transports,
        private readonly PayrollCalculatorRepository $calculators,
        private readonly Flusher $flusher,
    )
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->metrics->findAll() as $metric) {
            $calculator = new PayrollCalculator();
            $calculator->setName($metric->getName());
            $calculator->setDescription($metric->getDescription());
            $calculator->setValue($metric->getValue());
            $calculator->setSort($metric->getId());
            $calculator->setType("salary");
            $calculator->setTarget("service");
            $calculator->setProcessor(TherapyPayrollCalculatorProcessor::class);
            $this->calculators->add($calculator);
        }

        $this->flusher->flush();

        foreach ($this->kpis->findAll() as $kpi) {
            $calculator = new PayrollCalculator();
            $calculator->setName($kpi->getName());
            $calculator->setDescription($kpi->getDescription());
            $calculator->setValue($kpi->getValue());
            $calculator->setSort($kpi->getId());
            $calculator->setType("kpi");
            $calculator->setTarget("payroll");
            $calculator->setProcessor(TherapyPayrollCalculatorProcessor::class);
            $this->calculators->add($calculator);
        }

        $this->flusher->flush();

        foreach ($this->transports->findAll() as $transport) {
            $calculator = new PayrollCalculator();
            $calculator->setName($transport->getName());
            $calculator->setDescription($transport->getDescription());
            $calculator->setValue($transport->getValue());
            $calculator->setSort($transport->getId());
            $calculator->setType("transport");
            $calculator->setTarget("shift");
            $calculator->setProcessor(TherapyPayrollCalculatorProcessor::class);
            $this->calculators->add($calculator);
        }

        $this->flusher->flush();

        return Command::SUCCESS;
    }
}
