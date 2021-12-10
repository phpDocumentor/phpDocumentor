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

use Twig\Environment as Twig;

use function rtrim;
use function sprintf;

final class TemplateRenderer
{
    /** @var Twig */
    private $templatingEngine;

    /** @var string */
    private $basePath;

    public function __construct(Twig $templatingEngine, string $basePath)
    {
        $this->templatingEngine = $templatingEngine;
        $this->basePath = $basePath;
    }

    public function setDestination(string $path): void
    {
        $this->templatingEngine->addGlobal('destinationPath', $path);
    }

    /**
     * @param mixed[] $parameters
     */
    public function render(string $template, array $parameters = []): string
    {
        return rtrim(
            $this->templatingEngine->render(sprintf('%s/%s', $this->basePath, $template), $parameters),
            "\n"
        );
    }
}
