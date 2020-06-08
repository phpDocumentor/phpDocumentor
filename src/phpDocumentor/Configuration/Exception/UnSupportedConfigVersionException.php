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
use function implode;
use function sprintf;

/**
 * @codeCoverageIgnore
 */
final class UnSupportedConfigVersionException extends RuntimeException
{
    /**
     * @param string[] $supportedVersions
     */
    public static function create(string $configurationVersion, array $supportedVersions) : self
    {
        return new self(
            sprintf(
                'Configuration version "%s" is not supported by this version of phpDocumentor, '
                . 'supported versions are: %s',
                $configurationVersion,
                implode(', ', $supportedVersions)
            )
        );
    }
}
