<?php
declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Exception\NotFoundException;
use App\Domain\Interfaces\Repository\BinRepositoryInterface;
use App\Infrastructure\Exception\InfrastructureException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @codeCoverageIgnore
 */
class BinRepository implements BinRepositoryInterface
{
    private string $binListUrl;
    private SerializerInterface $serializer;

    public function __construct(string $binListUrl, SerializerInterface $serializer)
    {
        $this->binListUrl = $binListUrl;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getBinCountryCode(int $id): string
    {
        $binData = json_decode(file_get_contents(
            sprintf(
                '%s/%s',
                $this->binListUrl,
                $id
            )
        ), true);

        // TODO implement Bin class if needed and deserialize it
        if (!$binData) {
            throw new NotFoundException('Bin', ['id' => $id]);
        }

        if (empty($binData['country']) || empty($binData['country']['alpha2'])) {
            throw new InfrastructureException('Cannot get country code data from the source');
        }

        return $binData['country']['alpha2'];
    }
}
