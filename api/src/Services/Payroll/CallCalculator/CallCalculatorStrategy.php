<?php

declare(strict_types=1);

namespace App\Services\Payroll\CallCalculator;

use DomainException;

class CallCalculatorStrategy
{
    private array $strategies;

    public function __construct(
        RepeatCalculator $repeat,
        RepeatTransmittedCalculator $repeatTransmitted
    ) {
        $this->strategies = [
            'call_repeat' => $repeat,
            'call_repeat_transmitted' => $repeatTransmitted,
        ];
    }

    public function getStrategy($processor): CallCalculatorInterface
    {
        if (!isset($this->strategies[$processor])) {
            throw new DomainException(
                'Unknown payroll call strategy ' .
                $processor
            );
        }
        return $this->strategies[$processor];
    }
}
