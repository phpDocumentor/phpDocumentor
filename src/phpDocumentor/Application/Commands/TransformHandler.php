<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use League\Event\Emitter;
use League\Tactician\CommandBus;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Renderer\RenderActionCompleted;
use phpDocumentor\Renderer\RenderingFinished;
use phpDocumentor\Renderer\RenderingStarted;
use phpDocumentor\FilesystemFactory;
use phpDocumentor\Path;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Renderer\TemplateFactory;
use phpDocumentor\Transformer\Transformer;

final class TransformHandler
{
    const EVENT_PRE_TRANSFORMATION = 'transformer.transformation.pre';
    const EVENT_POST_TRANSFORMATION = 'transformer.transformation.post';
    const EVENT_PRE_INITIALIZATION = 'transformer.writer.initialization.pre';
    const EVENT_POST_INITIALIZATION = 'transformer.writer.initialization.post';
    const EVENT_PRE_TRANSFORM = 'transformer.transform.pre';
    const EVENT_POST_TRANSFORM = 'transformer.transform.post';

    /** @var Compiler */
    private $compiler;

    /** @var Transformer */
    private $transformer;

    /** @var Analyzer */
    private $analyzer;
    /**
     * @var TemplateFactory
     */
    private $templateFactory;
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var FilesystemFactory
     */
    private $filesystemFactory;
    /**
     * @var Dispatcher
     */
    private $emitter;

    public function __construct(
        Transformer     $transformer,
        Compiler        $compiler,
        Analyzer        $analyzer,
        TemplateFactory $templateFactory,
        CommandBus      $commandBus,
        FilesystemFactory $filesystemFactory,
        Emitter         $emitter
    ) {
        // v2
        $this->compiler        = $compiler;
        $this->transformer     = $transformer;
        $this->analyzer        = $analyzer;

        // v3
        $this->templateFactory   = $templateFactory;
        $this->commandBus        = $commandBus;
        $this->filesystemFactory = $filesystemFactory;
        $this->emitter           = $emitter;
    }

    public function __invoke(Transform $command)
    {
        if (isset($_SERVER['PHPDOC_V3'])) {
            $this->emitter->emit(new RenderingStarted());
            $destinationFilesystem = $this->filesystemFactory->create(
                new Dsn($command->getTarget()[0] === '/' ? '/' : '.')
            );
            $renderPass = new RenderPass($destinationFilesystem, new Path($command->getTarget()));
            $template   = $this->templateFactory->createFromName($renderPass, 'clean');
            foreach ($template->getActions() as $action) {
                $this->commandBus->handle($action);
                $this->emitter->emit(new RenderActionCompleted($action));
            }
            $this->emitter->emit(new RenderingFinished());
            return;
        }

        $this->transformer->setTarget($command->getTarget());

        /** @var CompilerPassInterface $pass */
        foreach ($this->compiler as $pass) {
            $pass->execute($this->analyzer->getProjectDescriptor());
        }
    }
}
