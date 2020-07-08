<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Interfaces\Repository\CountryCodeRepositoryInterface;

/**
 * @codeCoverageIgnore
 */
class CountryCodeRepository implements CountryCodeRepositoryInterface
{
    /**
     * @var string[]
     */
    private array $euCountryCodes;

    public function __construct(array $euCountryCodes)
    {
        $this->euCountryCodes = $euCountryCodes;
    }

    /**
     * @inheritDoc
     */
    public function getEuCountryCodes(): array
    {
        return $this->euCountryCodes;
    }
}
