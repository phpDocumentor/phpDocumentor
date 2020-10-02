<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Templates;

use Twig\Environment;
use function rtrim;

class TemplateRenderer
{
    /** @var Environment */
    private $environment;

    /** @var string */
    private $basePath;

    public function __construct(Environment $environment, string $basePath)
    {
        $this->environment = $environment;
        $this->basePath = $basePath;
    }

    public function getTemplateEngine(): Environment
    {
        return $this->environment;
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $template, array $parameters = []) : string
    {
        return rtrim($this->environment->render($this->basePath . '/' . $template, $parameters), "\n");
    }
}
