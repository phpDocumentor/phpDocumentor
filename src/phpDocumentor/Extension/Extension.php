<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\Manifest;

final class Extension
{
    /** @var Manifest */
    private $manifest;

    /** @var string */
    private $namespace;

    /** @var string */
    private $path;

    private function __construct(Manifest $manifest, string $path)
    {
        $this->manifest = $manifest;
        $this->path = $path;
        foreach ($manifest->getBundledComponents() as $component) {
            $this->namespace = trim($component->getName(), '\\') . '\\';
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
        return $this->namespace . 'Extension';
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
