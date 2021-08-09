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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TagDescriptor;

/**
 * @api
 * @package phpDocumentor\AST\Tags
 */
class MethodDescriptor extends TagDescriptor
{
    /** @var string */
    private $methodName = '';

    /** @var Collection<ArgumentDescriptor> */
    private $arguments;

    /** @var ?ReturnDescriptor */
    private $response;

    /** @var bool */
    private $static = false;

    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->arguments = Collection::fromClassString(ArgumentDescriptor::class);
    }

    public function setMethodName(string $methodName): void
    {
        $this->methodName = $methodName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @param Collection<ArgumentDescriptor> $arguments
     */
    public function setArguments(Collection $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return Collection<ArgumentDescriptor>
     */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function setResponse(?ReturnDescriptor $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?ReturnDescriptor
    {
        return $this->response;
    }

    public function setStatic(bool $static): void
    {
        $this->static = $static;
    }

    public function isStatic(): bool
    {
        return $this->static;
    }
}
