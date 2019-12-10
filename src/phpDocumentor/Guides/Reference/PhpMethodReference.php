<?php

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Reference;

use Doctrine\RST\Environment;
use Doctrine\RST\References\Reference;
use Doctrine\RST\References\ResolvedReference;

class PhpMethodReference extends Reference
{
    private $phpDocUrl;

    public function __construct(string $phpDocUrl)
    {
        $this->phpDocUrl = $phpDocUrl;
    }

    public function getName(): string
    {
        return 'phpmethod';
    }

    public function resolve(Environment $environment, string $data): ResolvedReference
    {
        $class = explode('::', $data)[0];
        $method = explode('::', $data)[1];

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $data.'()',
            sprintf('%s/%s.%s.php', $this->phpDocUrl, strtolower($class), strtolower($method)),
            [],
            [
                'title' => $class,
            ]
        );
    }
}
