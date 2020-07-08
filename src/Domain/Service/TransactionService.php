<?php
declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Exception\InfrastructureExceptionInterface;
use App\Domain\Exception\NotFoundException;
use App\Domain\Interfaces\Repository\BinRepositoryInterface;
use App\Domain\Interfaces\Repository\CountryCodeRepositoryInterface;
use App\Domain\Interfaces\Repository\RateRepositoryInterface;
use App\Domain\ValueObject\Transaction;

class TransactionService
{
    private BinRepositoryInterface $binRepository;
    private RateRepositoryInterface $rateRepository;
    private CountryCodeRepositoryInterface $countryCodeRepository;
    private string $defaultCurrencyCode;
    private float $euCommissionRate;
    private float $nonEuCommissionRate;

    public function __construct(
        BinRepositoryInterface $binRepository,
        RateRepositoryInterface $rateRepository,
        CountryCodeRepositoryInterface $countryCodeRepository,
        string $defaultCurrencyCode,
        float $euCommissionRate,
        float $nonEuCommissionRate
    ) {
        $this->binRepository = $binRepository;
        $this->rateRepository = $rateRepository;
        $this->countryCodeRepository = $countryCodeRepository;
        $this->defaultCurrencyCode = $defaultCurrencyCode;
        $this->euCommissionRate = $euCommissionRate;
        $this->nonEuCommissionRate = $nonEuCommissionRate;
    }

    /**
     * @throws NotFoundException
     * @throws InfrastructureExceptionInterface
     */
    public function calculateCommission(Transaction $transaction): float
    {
        $amountInDefaultCurrency = $this->calculateAmountInDefaultCurrency(
            $transaction->getCurrencyCode(),
            $transaction->getAmount()
        );

        $commissionRate = $this->getCommissionRate(
            $this->binRepository->getBinCountryCode($transaction->getBinId())
        );

        return $this->ceilAmountToRoundCents($amountInDefaultCurrency * $commissionRate);
    }

    /**
     * @throws InfrastructureExceptionInterface
     */
    private function calculateAmountInDefaultCurrency(string $currencyCode, float $amount): float
    {
        $rate = $currencyCode === $this->defaultCurrencyCode
            ? null
            : $this->rateRepository->getCurrencyRate($currencyCode);

        return $rate
            ? $amount / $rate
            : $amount;
    }

    private function getCommissionRate(string $countryCode): float
    {
        return in_array($countryCode, $this->countryCodeRepository->getEuCountryCodes())
            ? $this->euCommissionRate
            : $this->nonEuCommissionRate;
    }

    private function ceilAmountToRoundCents(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }
}
