<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Exception;

use RuntimeException;
use function sprintf;

/**
 * @codeCoverageIgnore
 */
final class UpgradeFailedException extends RuntimeException
{
    public static function create(string $currentVersion) : self
    {
        return new self(sprintf(
            'Upgrading the configuration to the latest version failed, we were unable to upgrade '
            . 'version "%s" to a later version',
            $currentVersion
        ));
    }
}
