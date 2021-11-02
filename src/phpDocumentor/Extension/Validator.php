<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ApplicationName;
use PharIo\Version\Version;
use phpDocumentor\Version as ApplicationVersion;

final class Validator
{
    /** @var ApplicationName */
    private $applicationName;

    /** @var Version */
    private $applicationVersion;

    public function __construct(ApplicationName $applicationName, ApplicationVersion $applicationVersion)
    {
        $this->applicationName    = $applicationName;
        $this->applicationVersion = new Version($applicationVersion->getExtensionVersion());
    }

    public function isValid(Extension $extension): bool
    {
        return $extension->getManifest()->isExtensionFor($this->applicationName, $this->applicationVersion);
    }
}
