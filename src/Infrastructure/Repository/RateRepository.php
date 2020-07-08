<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Interfaces\Repository\RateRepositoryInterface;
use App\Infrastructure\Exception\InfrastructureException;

/**
 * @codeCoverageIgnore
 */
class RateRepository implements RateRepositoryInterface
{
    private string $ratesUrl;
    /**
     * @var float[]
     */
    private array $rates;

    public function __construct(string $ratesUrl)
    {
        $this->ratesUrl = $ratesUrl;
    }

    /**
     * @inheritDoc
     */
    public function getCurrencyRate(string $currencyCode): ?float
    {
        if (empty($this->rates)) {
            $ratesData = json_decode(file_get_contents($this->ratesUrl), true);

            if (!$ratesData || empty($ratesData['rates'])) {
                throw new InfrastructureException('Cannot get rates data from the source');
            }

            $this->rates = $ratesData['rates'];
        }

        return $this->rates[$currencyCode] ?? null;
    }
}
