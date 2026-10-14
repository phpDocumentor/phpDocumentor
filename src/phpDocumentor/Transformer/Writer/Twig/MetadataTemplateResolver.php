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

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Reflection\Metadata\Metadata;
use Psr\Log\LoggerInterface;
use Twig\Loader\LoaderInterface;

use function in_array;
use function sprintf;

/**
 * Finds the template with which an extension renders a metadata object.
 *
 * Extensions declare the metadata keys they handle. The template of a key is found by convention in the Twig
 * namespace of the extension: `@<alias>/metadata/<key>.html.twig`.
 */
final class MetadataTemplateResolver
{
    /** @param array<string, array{path: string, keys: string[]}> $extensions indexed by the alias of the extension */
    public function __construct(
        private readonly array $extensions,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function resolve(Metadata $metadata, string $nodeName, LoaderInterface $loader): string|null
    {
        $key = $metadata->key();

        foreach ($this->extensions as $alias => $extension) {
            if (! in_array($key, $extension['keys'], true)) {
                continue;
            }

            $template = sprintf('@%s/metadata/%s.html.twig', $alias, $key);
            if ($loader->exists($template)) {
                return $template;
            }

            $this->logger->warning(
                sprintf(
                    'Metadata with key "%s" on "%s" is not rendered, extension "%s" has no template "%s"',
                    $key,
                    $nodeName,
                    $alias,
                    $template,
                ),
            );

            return null;
        }

        $this->logger->warning(
            sprintf('Metadata with key "%s" on "%s" is not rendered, no extension declares this key', $key, $nodeName),
        );

        return null;
    }
}
