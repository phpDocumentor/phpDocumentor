<?php

declare(strict_types=1);

namespace phpDocumentor;

use Jean85\PrettyVersions;
use OutOfBoundsException;

use function explode;
use function file_get_contents;
use function ltrim;
use function sprintf;
use function strpos;
use function trim;

final class Version
{
    private const VERSION = '@package_version@';

    /** @var string */
    private $version;

    public function __construct()
    {
        $this->version = $this->detectVersion();
    }

    private function detectVersion(): string
    {
        $version = self::VERSION;

        // prevent replacing the version by the PEAR building
        if (sprintf('%s%s%s', '@', 'package_version', '@') === self::VERSION) {
            $version = trim(file_get_contents(__DIR__ . '/../../VERSION'));
            // @codeCoverageIgnoreStart
            try {
                $packageVersion = PrettyVersions::getRootPackageVersion();
                if ($packageVersion->getPrettyVersion() === $version) {
                    return $version;
                }

                $version = sprintf(
                    '%s-%s+%s',
                    $version,
                    $packageVersion->getShortVersion(),
                    $packageVersion->getShortReference(),
                );
            } catch (OutOfBoundsException) {
            }

            // @codeCoverageIgnoreEnd
        }

        return $version;
    }

    public function getVersion(): string
    {
        return ltrim($this->version, 'v');
    }

    public function getExtensionVersion(): string
    {
        if (strpos($this->version, '-') !== false) {
            $version = explode('-', $this->version)[0];

            return $version . '-dev';
        }

        return $this->version;
    }
}
