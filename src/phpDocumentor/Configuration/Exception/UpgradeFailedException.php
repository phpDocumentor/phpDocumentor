<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Configuration\Exception;

use RuntimeException;

use function sprintf;

/** @codeCoverageIgnore */
final class UpgradeFailedException extends RuntimeException
{
    public static function create(string $currentVersion): self
    {
        return new self(sprintf(
            'Upgrading the configuration to the latest version failed, we were unable to upgrade '
            . 'version "%s" to a later version',
            $currentVersion,
        ));
    }
}
