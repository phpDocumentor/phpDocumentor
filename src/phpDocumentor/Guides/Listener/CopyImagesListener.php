<?php

declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Listener;

use Doctrine\RST\ErrorManager;
use Doctrine\RST\Event\PreNodeRenderEvent;
use Doctrine\RST\Nodes\ImageNode;
use Symfony\Component\Filesystem\Filesystem;
use phpDocumentor\Guides\BuildContext;

class CopyImagesListener
{
    private $buildContext;
    private $errorManager;

    public function __construct(BuildContext $buildContext, ErrorManager $errorManager)
    {
        $this->buildContext = $buildContext;
        $this->errorManager = $errorManager;
    }

    public function preNodeRender(PreNodeRenderEvent $event)
    {
        $node = $event->getNode();
        if (!$node instanceof ImageNode) {
            return;
        }

        $sourceImage = $node->getEnvironment()->absoluteRelativePath($node->getUrl());

        if (!file_exists($sourceImage)) {
            $this->errorManager->error(sprintf(
                'Missing image file "%s" in "%s"',
                $node->getUrl(),
                $node->getEnvironment()->getCurrentFileName()
            ));

            return;
        }

        $fileInfo = new \SplFileInfo($sourceImage);
        $fs = new Filesystem();

        // the /_images path is currently hardcoded here and respected
        // in the overridden image node template
        $newPath = '/_images/'.$fileInfo->getFilename();
        $fs->copy($sourceImage, $this->buildContext->getOutputDir().$newPath, true);

        $node->setValue($node->getEnvironment()->relativeUrl(
            '/_images/'.$fileInfo->getFilename()
        ));
    }
}
