<?php
declare(strict_types=1);

namespace App\Services;

use App\Domain\ValueObject\Transaction;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @codeCoverageIgnore
 */
class TransactionsFileReader
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @return Transaction[]
     */
    public function getTransactions(string $filepath): array
    {
        $transactionLines = file($filepath) ?? [];

        $transactions = [];

        foreach ($transactionLines as $transactionLine) {
           $transactions[] = $this->serializer->deserialize(
                $transactionLine,
                Transaction::class,
                'json'
           );
        }

        return $transactions;
    }
}
