<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\References;

use phpDocumentor\Guides\RestructuredText\Environment;

class Doc extends Reference
{
    /** @var string */
    private $name;

    /** @var Resolver */
    private $resolver;

    /**
     * Used with "ref" - it means the dependencies added in found()
     * must be resolved to their final path later (they are not
     * already document names).
     *
     * @var bool
     */
    private $dependenciesMustBeResolved;

    public function __construct(string $name = 'doc', bool $dependenciesMustBeResolved = false)
    {
        $this->name                       = $name;
        $this->resolver                   = new Resolver();
        $this->dependenciesMustBeResolved = $dependenciesMustBeResolved;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function resolve(Environment $environment, string $data) : ?ResolvedReference
    {
        return $this->resolver->resolve($environment, $data);
    }

    public function found(Environment $environment, string $data) : void
    {
        $environment->addDependency($data, $this->dependenciesMustBeResolved);
    }
}
