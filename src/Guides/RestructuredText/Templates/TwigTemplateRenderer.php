<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Templates;

use phpDocumentor\Guides\RestructuredText\Configuration;
use function rtrim;

class TwigTemplateRenderer implements TemplateRenderer
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $template, array $parameters = []) : string
    {
        return rtrim($this->configuration->getTemplateEngine()->render($template, $parameters), "\n");
    }
}
