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

class MethodReference extends Reference
{
    private $symfonyApiUrl;

    public function __construct(string $symfonyApiUrl)
    {
        $this->symfonyApiUrl = $symfonyApiUrl;
    }

    public function getName(): string
    {
        return 'method';
    }

    public function resolve(Environment $environment, string $data): ResolvedReference
    {
        $className = explode('::', $data)[0];
        $className = str_replace('\\\\', '\\', $className);

        if (false === strpos($data, '::')) {
            throw new \RuntimeException(sprintf('Malformed method reference  "%s" in file "%s"', $data, $environment->getCurrentFileName()));
        }

        $methodName = explode('::', $data)[1];

        return new ResolvedReference(
            $environment->getCurrentFileName(),
            $methodName.'()',
            sprintf('%s/%s.html#method_%s', $this->symfonyApiUrl, str_replace('\\', '/', $className), $methodName),
            [],
            [
                'title' => sprintf('%s::%s()', $className, $methodName),
            ]
        );
    }
}
