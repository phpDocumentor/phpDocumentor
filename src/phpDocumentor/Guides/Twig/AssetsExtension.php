<?php

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetsExtension extends AbstractExtension
{
    /** @var string */
    private $htmlOutputDir;

    public function __construct(string $htmlOutputDir)
    {
        $this->htmlOutputDir = $htmlOutputDir;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', [$this, 'asset'], ['is_safe' => ['html']]),
        ];
    }

    public function asset($path)
    {
        return sprintf('%s/assets/%s', $this->htmlOutputDir, $path);
    }
}
