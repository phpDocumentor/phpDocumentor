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

namespace phpDocumentor\Guides;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Descriptor\GuideSetDescriptor;

final class RenderCommand
{
    /** @var GuideSetDescriptor */
    private $documentationSet;

    /** @var FilesystemInterface */
    private $origin;

    /** @var FilesystemInterface */
    private $destination;

    public function __construct(
        GuideSetDescriptor $documentationSet,
        FilesystemInterface $origin,
        FilesystemInterface $destination
    ) {
        $this->destination = $destination;
        $this->documentationSet = $documentationSet;
        $this->origin = $origin;
    }

    public function getDocumentationSet(): GuideSetDescriptor
    {
        return $this->documentationSet;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->destination;
    }

    public function getDestinationPath(): string
    {
        return $this->documentationSet->getOutputLocation();
    }

    public function getTargetFileFormat(): string
    {
        return $this->documentationSet->getOutputFormat();
    }
}
