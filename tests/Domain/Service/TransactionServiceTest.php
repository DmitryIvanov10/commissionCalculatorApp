<?php
declare(strict_types=1);

namespace App\Tests\Domain\Service;

use App\Domain\Exception\DomainExceptionInterface;
use App\Domain\Exception\InfrastructureExceptionInterface;
use App\Domain\Exception\NotFoundException;
use App\Domain\Interfaces\Repository\BinRepositoryInterface;
use App\Domain\Interfaces\Repository\CountryCodeRepositoryInterface;
use App\Domain\Interfaces\Repository\RateRepositoryInterface;
use App\Domain\Service\TransactionService;
use App\Domain\ValueObject\Transaction;
use App\Infrastructure\Exception\InfrastructureException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransactionServiceTest extends TestCase
{
    private const DEFAULT_CURRENCY_CODE = 'EUR';
    private const EU_COMMISSION_RATE = 0.01;
    private const NON_EU_COMMISSION_RATE = 0.02;
    private const EU_COUNTRY_CODE = 'PL';
    private const NON_EU_COUNTRY_CODE = 'UA';

    /**
     * @var Transaction|MockObject
     */
    private MockObject $transactionMock;
    /**
     * @var BinRepositoryInterface|MockObject
     */
    private MockObject $binRepositoryMock;
    /**
     * @var RateRepositoryInterface|MockObject
     */
    private MockObject $rateRepositoryMock;
    /**
     * @var CountryCodeRepositoryInterface|MockObject
     */
    private MockObject $countryCodeRepositoryMock;

    private TransactionService $service;

    protected function setUp()
    {
        parent::setUp();

        $this->transactionMock = $this->createMock(Transaction::class);
        $this->binRepositoryMock = $this->createMock(BinRepositoryInterface::class);
        $this->rateRepositoryMock = $this->createMock(RateRepositoryInterface::class);
        $this->countryCodeRepositoryMock = $this->createMock(CountryCodeRepositoryInterface::class);

        $this->service = new TransactionService(
            $this->binRepositoryMock,
            $this->rateRepositoryMock,
            $this->countryCodeRepositoryMock,
            self::DEFAULT_CURRENCY_CODE,
            self::EU_COMMISSION_RATE,
            self::NON_EU_COMMISSION_RATE
        );
    }

    /**
     * @dataProvider calculateCommissionDataProvider
     */
    public function testCalculateCommission(
        string $transactionCurrencyCode,
        string $transactionCountryCode,
        float $transactionAmount,
        ?float $currencyRate,
        float $expectedPrice
    ) {
        $transactionBinId = 1;
        $euCountryCodes = [self::EU_COUNTRY_CODE];

        $this->setPropertiesToTransactionMock($transactionBinId, $transactionAmount, $transactionCurrencyCode);

        $this->binRepositoryMock
            ->expects($this->any())
            ->method('getBinCountryCode')
            ->with($transactionBinId)
            ->willReturn($transactionCountryCode);

        $this->rateRepositoryMock
            ->expects($this->any())
            ->method('getCurrencyRate')
            ->with($transactionCurrencyCode)
            ->willReturn($currencyRate);

        $this->countryCodeRepositoryMock
            ->expects($this->any())
            ->method('getEuCountryCodes')
            ->willReturn($euCountryCodes);

        try {
            $this->assertEquals($expectedPrice, $this->service->calculateCommission($this->transactionMock));
        } catch (DomainExceptionInterface $exception) {
            $this->fail('Unexpected behavior of the Transaction service');
        }
    }

    /**
     * @throws NotFoundException
     */
    public function testCalculateCommissionWithNotFoundException() {
        $transactionBinId = 1;
        $transactionAmount = 100;
        $transactionCurrencyCode = self::DEFAULT_CURRENCY_CODE;
        $euCountryCodes = [self::EU_COUNTRY_CODE];

        $this->setPropertiesToTransactionMock($transactionBinId, $transactionAmount, $transactionCurrencyCode);

        $this->binRepositoryMock
            ->expects($this->any())
            ->method('getBinCountryCode')
            ->with($transactionBinId)
            ->willThrowException(new NotFoundException('Bin'));

        $this->rateRepositoryMock
            ->expects($this->any())
            ->method('getCurrencyRate')
            ->with($transactionCurrencyCode)
            ->willReturn(null);

        $this->countryCodeRepositoryMock
            ->expects($this->any())
            ->method('getEuCountryCodes')
            ->willReturn($euCountryCodes);

        $this->expectException(NotFoundException::class);

        try {
            $this->service->calculateCommission($this->transactionMock);
        } catch (InfrastructureExceptionInterface $exception) {
            $this->fail('Unexpected behavior of the Transaction service');
        }
    }

    /**
     * @throws InfrastructureExceptionInterface
     */
    public function testCalculateCommissionWithInfrastructureException() {
        $transactionBinId = 1;
        $transactionAmount = 100;
        $transactionCurrencyCode = self::DEFAULT_CURRENCY_CODE;
        $euCountryCodes = [self::EU_COUNTRY_CODE];

        $this->setPropertiesToTransactionMock($transactionBinId, $transactionAmount, $transactionCurrencyCode);

        $this->binRepositoryMock
            ->expects($this->any())
            ->method('getBinCountryCode')
            ->with($transactionBinId)
            ->willThrowException(new InfrastructureException());

        $this->rateRepositoryMock
            ->expects($this->any())
            ->method('getCurrencyRate')
            ->with($transactionCurrencyCode)
            ->willReturn(null);

        $this->countryCodeRepositoryMock
            ->expects($this->any())
            ->method('getEuCountryCodes')
            ->willReturn($euCountryCodes);

        $this->expectException(InfrastructureExceptionInterface::class);

        try {
            $this->service->calculateCommission($this->transactionMock);
        } catch (NotFoundException $exception) {
            $this->fail('Unexpected behavior of the Transaction service');
        }
    }

    public function calculateCommissionDataProvider()
    {
        return [
            'default_currency_eu_country' => [
                'transactionCurrencyCode' => self::DEFAULT_CURRENCY_CODE,
                'transactionCountryCode' => self::EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => null,
                'expectedAmount' => 1
            ],
            'default_currency_not_eu_country' => [
                'transactionCurrencyCode' => self::DEFAULT_CURRENCY_CODE,
                'transactionCountryCode' => self::NON_EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => null,
                'expectedAmount' => 2
            ],
            'non_default_currency_eu_country_currency_rate_not_found' => [
                'transactionCurrencyCode' => 'ABC',
                'transactionCountryCode' => self::EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => null,
                'expectedAmount' => 1
            ],
            'non_default_currency_eu_country_currency_rate_found' => [
                'transactionCurrencyCode' => 'ABC',
                'transactionCountryCode' => self::EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => 0.9,
                'expectedAmount' => 1.12
            ],
            'non_default_currency_not_eu_country_currency_rate_not_found' => [
                'transactionCurrencyCode' => 'ABC',
                'transactionCountryCode' => self::NON_EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => null,
                'expectedAmount' => 2
            ],
            'non_default_currency_not_eu_country_currency_rate_found' => [
                'transactionCurrencyCode' => 'ABC',
                'transactionCountryCode' => self::NON_EU_COUNTRY_CODE,
                'transactionAmount' => 100,
                'currencyRate' => 1.1,
                'expectedAmount' => 1.82
            ]
        ];
    }

    private function setPropertiesToTransactionMock(
        int $transactionBinId,
        float $transactionAmount,
        string $transactionCurrencyCode
    ): void {
        $this->transactionMock
            ->expects($this->any())
            ->method('getBinId')
            ->willReturn($transactionBinId);
        $this->transactionMock
            ->expects($this->any())
            ->method('getAmount')
            ->willReturn($transactionAmount);
        $this->transactionMock
            ->expects($this->any())
            ->method('getCurrencyCode')
            ->willReturn($transactionCurrencyCode);
    }
}
