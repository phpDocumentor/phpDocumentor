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

namespace phpDocumentor\Guides\Renderer;

use phpDocumentor\Guides\Twig\EnvironmentBuilder;

use function rtrim;
use function sprintf;

final class TemplateRenderer
{
    /** @var string */
    private $basePath;
    private EnvironmentBuilder $builder;

    public function __construct(EnvironmentBuilder $builder, string $basePath = 'guides')
    {
        $this->basePath = $basePath;
        $this->builder = $builder;
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $template, array $parameters = []): string
    {
        return rtrim(
            $this->builder->getTwigEnvironment()->render(sprintf('%s/%s', $this->basePath, $template), $parameters),
            "\n"
        );
    }
}
