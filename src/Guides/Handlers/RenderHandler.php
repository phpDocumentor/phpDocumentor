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

namespace phpDocumentor\Guides\Handlers;

use InvalidArgumentException;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Documents;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\RenderCommand;
use function dirname;
use function sprintf;

final class RenderHandler
{
    /** @var Documents */
    private $documents;

    /** @var Metas */
    private $metas;

    public function __construct(Metas $metas, Documents $documents)
    {
        $this->metas = $metas;
        $this->documents = $documents;
    }

    public function handle(RenderCommand $command) : void
    {
        $this->render($command->getDestination());
    }

    public function render(FilesystemInterface $destination) : void
    {
        foreach ($this->documents->getAll() as $file => $document) {
            $target = $this->getTargetOf($file);

            $directory = dirname($target);

            if ($destination->has($directory)) {
                $destination->createDir($directory);
            }

            $destination->put($target, $document->renderDocument());
        }
    }

    private function getTargetOf(string $file) : string
    {
        $metaEntry = $this->metas->get($file);

        if ($metaEntry === null) {
            throw new InvalidArgumentException(sprintf('Could not find target file for %s', $file));
        }

        return $metaEntry->getUrl();
    }
}
