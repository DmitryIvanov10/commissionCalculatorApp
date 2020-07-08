<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * @codeCoverageIgnore
 */
class Transaction
{
    private int $binId;
    private float $amount;
    private string $currencyCode;

    public function __construct(int $binId, float $amount, string $currencyCode)
    {
        $this->binId = $binId;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
    }

    public function getBinId(): int
    {
        return $this->binId;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }
}
