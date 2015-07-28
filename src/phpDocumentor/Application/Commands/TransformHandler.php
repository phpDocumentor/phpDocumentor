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
use phpDocumentor\Dsn;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FilesystemFactory;
use phpDocumentor\Path;
use phpDocumentor\Renderer\TemplateFactory;
use phpDocumentor\Renderer\RenderPass;
use phpDocumentor\Renderer\RenderActionCompleted;
use phpDocumentor\Renderer\RenderingFinished;
use phpDocumentor\Renderer\RenderingStarted;

final class TransformHandler
{
    /** @var TemplateFactory */
    private $templateFactory;

    /** @var CommandBus */
    private $commandBus;

    /** @var FilesystemFactory */
    private $filesystemFactory;

    /** @var Dispatcher */
    private $emitter;

    public function __construct(
        TemplateFactory   $templateFactory,
        CommandBus        $commandBus,
        FilesystemFactory $filesystemFactory,
        Emitter           $emitter
    ) {
        $this->templateFactory   = $templateFactory;
        $this->commandBus        = $commandBus;
        $this->filesystemFactory = $filesystemFactory;
        $this->emitter           = $emitter;
    }

    public function __invoke(Transform $command)
    {
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
    }
}
