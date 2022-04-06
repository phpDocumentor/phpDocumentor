<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Twig;

use phpDocumentor\Guides\RenderContext;
use Twig\Environment;

class EnvironmentBuilder
{
    private Environment $environment;

    public function setEnvironmentFactory(callable $factory): void
    {
        $this->environment = $factory();
    }

    public function setContext(RenderContext $context): void
    {
        $this->environment->addGlobal('env', $context);
    }

    public function getTwigEnvironment(): Environment
    {
        return $this->environment;
    }
}
