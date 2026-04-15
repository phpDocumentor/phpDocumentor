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

namespace phpDocumentor\Extension;

use PharIo\Manifest\ApplicationName;
use PharIo\Version\Version;
use phpDocumentor\Version as ApplicationVersion;

final class Validator
{
    private Version $applicationVersion;

    public function __construct(private ApplicationName $applicationName, ApplicationVersion $applicationVersion)
    {
        $this->applicationVersion = new Version($applicationVersion->getExtensionVersion());
    }

    public function isValid(ExtensionInfo $extension): bool
    {
        return $extension->getManifest()->isExtensionFor($this->applicationName, $this->applicationVersion);
    }
}
