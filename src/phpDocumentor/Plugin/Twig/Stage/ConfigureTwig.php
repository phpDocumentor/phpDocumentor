<?php

declare(strict_types=1);

namespace phpDocumentor\Plugin\Twig\Stage;

use Twig\Environment;

final class ConfigureTwig
{
    /** @var Environment */
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function __invoke(array $configuration): array
    {
        // set the cache directory to be a subdirectory of phpDocumentor's cache, this will make it configurable
        // to be per-project instead of system-wide.
        $this->twig->setCache((string)$configuration['phpdocumentor']['paths']['cache'] . '/twig');

        return $configuration;
    }
}
