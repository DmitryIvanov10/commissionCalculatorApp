<?php
declare(strict_types=1);

namespace App\Domain\Interfaces\Repository;

use App\Domain\Exception\InfrastructureExceptionInterface;

interface RateRepositoryInterface
{
    /**
     * @throws InfrastructureExceptionInterface
     */
    public function getCurrencyRate(string $currencyCode): ?float;
}
