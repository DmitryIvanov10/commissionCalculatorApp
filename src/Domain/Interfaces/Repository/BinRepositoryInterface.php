<?php
declare(strict_types=1);

namespace App\Domain\Interfaces\Repository;

use App\Domain\Exception\InfrastructureExceptionInterface;
use App\Domain\Exception\NotFoundException;

interface BinRepositoryInterface
{
    /**
     * @throws NotFoundException
     * @throws InfrastructureExceptionInterface
     */
    public function getBinCountryCode(int $id): string ;
}
