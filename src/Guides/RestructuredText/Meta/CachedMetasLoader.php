<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Meta;

use LogicException;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function serialize;
use function sprintf;
use function unserialize;

final class CachedMetasLoader
{
    public function loadCachedMetaEntries(string $targetDirectory, Metas $metas) : void
    {
        $metaCachePath = $this->getMetaCachePath($targetDirectory);
        if (! file_exists($metaCachePath)) {
            return;
        }

        $contents = file_get_contents($metaCachePath);

        if ($contents === false) {
            throw new LogicException(sprintf('Could not load file "%s"', $contents));
        }

        $metas->setMetaEntries(unserialize($contents));
    }

    public function cacheMetaEntries(string $targetDirectory, Metas $metas) : void
    {
        file_put_contents($this->getMetaCachePath($targetDirectory), serialize($metas->getAll()));
    }

    private function getMetaCachePath(string $targetDirectory) : string
    {
        return $targetDirectory . '/metas.php';
    }
}
