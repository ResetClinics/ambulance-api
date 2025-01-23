<?php

declare(strict_types=1);

namespace App\Services\Payroll;

use App\Entity\Calling\Calling;
use App\Entity\Payroll\KpiDocument\KpiDocument;
use App\Entity\Payroll\KpiDocument\KpiRecord;
use App\Entity\User\User;
use App\Repository\CallingRepository;

readonly class KpiPayrollCalculator
{
    public function __construct(
        private readonly CallingRepository $calls
    ) {}

    public function calculate(KpiDocument $document): void
    {
        $document->clearRecords();

        $calls = $this->calls->findAllCompletedByCompletionDateIncludedInPeriod(
            $document->getPeriodStart(),
            $document->getPeriodEnd()
        );

        $users = [];

        /** @var Calling $call */
        foreach ($calls as $call) {
            $user = $call->getAdmin();

            if ($user && !isset($users[$user->getId()])) {
                $users[$user->getId()] = $user;
            }

            $user = $call->getDoctor();

            if ($user && !isset($users[$user->getId()])) {
                $users[$user->getId()] = $user;
            }
        }

        /** @var User $user */
        foreach ($users as $user) {
            $record = new KpiRecord($document, $user);
            $document->addRecord($record);
        }
    }
}
