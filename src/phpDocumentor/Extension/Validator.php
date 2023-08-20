<?php

declare(strict_types=1);

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
