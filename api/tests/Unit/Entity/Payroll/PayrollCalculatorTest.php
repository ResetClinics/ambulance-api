<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity\Payroll;

use App\Entity\Payroll\PayrollCalculator;
use App\Entity\Payroll\PayrollCalculatorValueHistory;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class PayrollCalculatorTest extends TestCase
{
    public function testGetValueForDateUsesHistoryAndFallsBackToCurrentValue(): void
    {
        $calculator = (new PayrollCalculator())->setValue('0.10');

        $firstHistory = (new PayrollCalculatorValueHistory())
            ->setEffectiveFrom(new DateTimeImmutable('2015-01-01'))
            ->setValue('0.15');
        $secondHistory = (new PayrollCalculatorValueHistory())
            ->setEffectiveFrom(new DateTimeImmutable('2026-04-01'))
            ->setValue('0.25');

        $calculator->addValueHistory($firstHistory);
        $calculator->addValueHistory($secondHistory);

        self::assertSame('0.15', $calculator->getValueForDate(new DateTimeImmutable('2026-03-31 10:00:00')));
        self::assertSame('0.25', $calculator->getValueForDate(new DateTimeImmutable('2026-04-01 18:00:00')));
        self::assertSame('0.10', $calculator->getValueForDate(new DateTimeImmutable('2014-12-31 23:59:59')));
    }

    public function testGetRatesForDateUsesHistoricalJsonValue(): void
    {
        $calculator = (new PayrollCalculator())->setValue('[{"min":0,"max":100,"rate":0.1}]');

        $history = (new PayrollCalculatorValueHistory())
            ->setEffectiveFrom(new DateTimeImmutable('2026-01-01'))
            ->setValue('[{"min":0,"max":50,"rate":0.2}]');

        $calculator->addValueHistory($history);

        self::assertSame(
            [['min' => 0.0, 'max' => 50.0, 'rate' => 0.2]],
            $calculator->getRatesForDate(new DateTimeImmutable('2026-02-01'))
        );
    }
}
