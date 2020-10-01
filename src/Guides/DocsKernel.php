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

namespace phpDocumentor\Guides;

use Doctrine\Common\EventManager;
use IteratorAggregate;
use phpDocumentor\Guides\RestructuredText\Listener\AssetsCopyListener;
use phpDocumentor\Guides\RestructuredText\Listener\CopyImagesListener;
use phpDocumentor\Guides\RestructuredText\Builder;
use phpDocumentor\Guides\RestructuredText\Configuration;
use phpDocumentor\Guides\RestructuredText\ErrorManager;
use phpDocumentor\Guides\RestructuredText\Event\PostBuildRenderEvent;
use phpDocumentor\Guides\RestructuredText\Event\PreNodeRenderEvent;
use phpDocumentor\Guides\RestructuredText\Kernel;

class DocsKernel extends Kernel
{
    private $buildContext;

    public function __construct(
        Configuration $configuration,
        IteratorAggregate $directives,
        IteratorAggregate $references,
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

    private function initializeListeners(EventManager $eventManager, ErrorManager $errorManager) : void
    {
        $eventManager->addEventListener(
            PreNodeRenderEvent::PRE_NODE_RENDER,
            new CopyImagesListener($this->buildContext, $errorManager)
        );

        $eventManager->addEventListener(
            [PostBuildRenderEvent::POST_BUILD_RENDER],
            new AssetsCopyListener($this->buildContext->getOutputFilesystem())
        );
    }
}
