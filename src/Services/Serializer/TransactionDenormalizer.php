<?php
declare(strict_types=1);

namespace App\Services\Serializer;

use App\Domain\Exception\DomainException;
use App\Domain\ValueObject\Transaction;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @codeCoverageIgnore
 */
class TransactionDenormalizer implements DenormalizerInterface
{
    /**
     * @throws DomainException
     */
    public function denormalize($data, $type, $format = null, array $context = [])
    {
        if (array_key_exists('bin', $data)
            && array_key_exists('amount', $data)
            && array_key_exists('currency', $data)
        ) {
            return new Transaction(
                (int)$data['bin'],
                (float)$data['amount'],
                $data['currency']
            );
        }

        throw new DomainException('Cannot get Transaction data');
    }

    public function supportsDenormalization($data, $type, $format = null)
    {
        return $type === Transaction::class;
    }
}
