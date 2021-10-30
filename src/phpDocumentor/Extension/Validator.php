<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ApplicationName;
use PharIo\Manifest\Manifest;
use PharIo\Version\Version;

final class Validator
{
    /** @var ApplicationName */
    private $applicationName;

    /** @var Version */
    private $applicationVersion;

    public function __construct(ApplicationName $applicationName)
    {
        $this->applicationName    = $applicationName;
        $this->applicationVersion = new Version((new \phpDocumentor\Version())->getExtensionVersion());
    }

    public function isValid(Manifest $manifest): bool
    {
        return $manifest->isExtensionFor($this->applicationName, $this->applicationVersion);
    }
}
