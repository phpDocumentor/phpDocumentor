<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Listener;

use Doctrine\RST\ErrorManager;
use Doctrine\RST\Event\PreNodeRenderEvent;
use Doctrine\RST\Nodes\ImageNode;
use phpDocumentor\Guides\BuildContext;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

class CopyImagesListener
{
    private $buildContext;
    private $errorManager;

    public function __construct(BuildContext $buildContext, ErrorManager $errorManager)
    {
        $this->buildContext = $buildContext;
        $this->errorManager = $errorManager;
    }

    public function preNodeRender(PreNodeRenderEvent $event) : void
    {
        $node = $event->getNode();
        if (!$node instanceof ImageNode) {
            return;
        }

        $sourceImage = $node->getEnvironment()->absoluteRelativePath($node->getUrl());

        if (!file_exists($sourceImage)) {
            $this->errorManager->error(
                sprintf(
                    'Missing image file "%s" in "%s"',
                    $node->getUrl(),
                    $node->getEnvironment()->getCurrentFileName()
                )
            );

            return;
        }

        $fileInfo = new SplFileInfo($sourceImage);
        $fs = new Filesystem();

        // the /_images path is currently hardcoded here and respected
        // in the overridden image node template
        $newPath = '/_images/' . $fileInfo->getFilename();
        $fs->copy($sourceImage, $this->buildContext->getOutputFilesystem() . $newPath, true);

        $node->setValue(
            $node->getEnvironment()->relativeUrl(
                '/_images/' . $fileInfo->getFilename()
            )
        );
    }
}
