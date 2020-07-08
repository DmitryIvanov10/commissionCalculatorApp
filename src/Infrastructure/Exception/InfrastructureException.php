<?php
declare(strict_types=1);

namespace App\Infrastructure\Exception;

use Exception;
use App\Domain\Exception\InfrastructureExceptionInterface;

/**
 * @codeCoverageIgnore
 */
class InfrastructureException extends Exception implements InfrastructureExceptionInterface
{
}
