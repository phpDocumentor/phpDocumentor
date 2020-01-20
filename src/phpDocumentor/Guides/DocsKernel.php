<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides;

use Doctrine\Common\EventManager;
use Doctrine\RST\Builder;
use Doctrine\RST\Configuration;
use Doctrine\RST\ErrorManager;
use Doctrine\RST\Event\PostBuildRenderEvent;
use Doctrine\RST\Event\PreNodeRenderEvent;
use Doctrine\RST\Kernel;
use phpDocumentor\Guides\Listener\AssetsCopyListener;
use phpDocumentor\Guides\Listener\CopyImagesListener;

class DocsKernel extends Kernel
{
    private $buildContext;

    public function __construct(
        ?Configuration $configuration = null,
        iterable $directives = [],
        iterable $references = [],
        BuildContext $buildContext
    ) {
        parent::__construct($configuration, iterator_to_array($directives), iterator_to_array($references));

        $this->buildContext = $buildContext;
    }

    public function initBuilder(Builder $builder) : void
    {
        $this->initializeListeners(
            $builder->getConfiguration()->getEventManager(),
            $builder->getErrorManager()
        );
    }

    private function initializeListeners(EventManager $eventManager, ErrorManager $errorManager)
    {
        $eventManager->addEventListener(
            PreNodeRenderEvent::PRE_NODE_RENDER,
            new CopyImagesListener($this->buildContext, $errorManager)
        );

        if (!$this->buildContext->getParseSubPath()) {
            $eventManager->addEventListener(
                [PostBuildRenderEvent::POST_BUILD_RENDER],
                new AssetsCopyListener($this->buildContext->getOutputFilesystem())
            );
        }
    }
}
