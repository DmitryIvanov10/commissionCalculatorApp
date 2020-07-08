<?php
declare(strict_types=1);

namespace App\Domain\Interfaces\Repository;

interface CountryCodeRepositoryInterface
{
    /**
     * @return string[]
     */
    public function getEuCountryCodes(): array;
}
