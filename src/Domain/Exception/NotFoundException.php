<?php
declare(strict_types=1);

namespace App\Domain\Exception;

use Throwable;

/**
 * @codeCoverageIgnore
 */
class NotFoundException extends DomainException
{
    private const MESSAGE_FORMAT = "Cannot find Model %s with filter by: '%s'";

    public function __construct(string $className, array $filter = [], Throwable $previous = null)
    {
        parent::__construct(sprintf(
            self::MESSAGE_FORMAT,
            $className, http_build_query($filter, '', '; ')
        ), 0, $previous);
    }
}
