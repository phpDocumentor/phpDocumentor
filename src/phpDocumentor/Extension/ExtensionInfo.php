<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\Manifest;

use function array_pop;
use function explode;
use function implode;

final class ExtensionInfo
{
    /** @var Manifest */
    private $manifest;

    /** @var string */
    private $namespace;

    /** @var string */
    private $path;

    /** @var string */
    private $name;

    private function __construct(Manifest $manifest, string $path)
    {
        $this->manifest = $manifest;
        $this->path = $path;
        foreach ($manifest->getBundledComponents() as $component) {
            $parts = explode('\\', $component->getName());
            $this->name = array_pop($parts);
            $this->namespace = implode('\\', $parts) . '\\';
            break;
        }
    }

    public static function fromManifest(Manifest $manifest, string $path): self
    {
        return new self($manifest, $path);
    }

    public function getName(): string
    {
        return $this->manifest->getName()->asString();
    }

    public function getVersion(): string
    {
        return $this->manifest->getVersion()->getVersionString();
    }

    public function getExtensionClass(): string
    {
        return $this->namespace . $this->name;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getManifest(): Manifest
    {
        return $this->manifest;
    }
}
