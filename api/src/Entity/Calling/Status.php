<?php

declare(strict_types=1);

namespace App\Entity\Calling;

use Webmozart\Assert\Assert;

class Status
{
    public const ASSIGNED = 'assigned';
    public const ACCEPTED = 'accepted';

    public const DISPATCHED = 'dispatched';

    public const ARRIVED = 'arrived';
    public const COMPLETED = 'completed';
    public const REJECTED = 'rejected';

    private string $name;

    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::ASSIGNED,
            self::ACCEPTED,
            self::DISPATCHED,
            self::REJECTED,
            self::ARRIVED,
            self::COMPLETED,
        ]);

        $this->name = $name;
    }

    public static function assigned(): self
    {
        return new self(self::ASSIGNED);
    }

    public static function accepted(): self
    {
        return new self(self::ACCEPTED);
    }

    public static function dispatched(): self
    {
        return new self(self::DISPATCHED);
    }

    public static function rejected(): self
    {
        return new self(self::REJECTED);
    }

    public static function arrived(): self
    {
        return new self(self::ARRIVED);
    }

    public static function completed(): self
    {
        return new self(self::COMPLETED);
    }

    public function isEqual(self $other): bool
    {
        return $this->getName() === $other->getName();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isAssigned(): bool
    {
        return $this->name === self::ASSIGNED;
    }

    public function isAccepted(): bool
    {
        return $this->name === self::ACCEPTED;
    }

    public function isDispatched(): bool
    {
        return $this->name === self::DISPATCHED;
    }

    public function isRejected(): bool
    {
        return $this->name === self::REJECTED;
    }

    public function isArrived(): bool
    {
        return $this->name === self::ARRIVED;
    }

    public function isCompleted(): bool
    {
        return $this->name === self::COMPLETED;
    }
}
