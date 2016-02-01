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

namespace phpDocumentor\DomainModel;

use League\Event\Emitter;
use League\Tactician\CommandBus;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Render;
use phpDocumentor\Infrastructure\FileSystemFactory;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Renderer\TemplateFactory;
use phpDocumentor\DomainModel\Renderer\Template\RenderPass;
use phpDocumentor\DomainModel\Renderer\RenderActionCompleted;
use phpDocumentor\DomainModel\Renderer\RenderingFinished;
use phpDocumentor\DomainModel\Renderer\RenderingStarted;

final class RenderHandler
{
    /** @var TemplateFactory */
    private $templateFactory;

    /** @var CommandBus */
    private $commandBus;

    /** @var FileSystemFactory */
    private $filesystemFactory;

    /** @var Emitter */
    private $emitter;

    public function __construct(
        TemplateFactory   $templateFactory,
        CommandBus        $commandBus,
        FileSystemFactory $filesystemFactory,
        Emitter           $emitter
    ) {
        $this->templateFactory   = $templateFactory;
        $this->commandBus        = $commandBus;
        $this->filesystemFactory = $filesystemFactory;
        $this->emitter           = $emitter;
    }

    public function __invoke(Render $command)
    {
        $this->emitter->emit(new RenderingStarted());
        $destinationFilesystem = $this->filesystemFactory->create(
            new Dsn($command->getTarget()[0] === '/' ? '/' : '.')
        );

        $renderPass = new RenderPass(
            $destinationFilesystem,
            new Path($command->getTarget()),
            $command->getDocumentation()
        );

        foreach ($command->getTemplates() as $templateName) {
            $this->renderTemplate($renderPass, $templateName);
        }
        $this->emitter->emit(new RenderingFinished());
    }

    /**
     * @param RenderPass $renderPass
     * @param string $templateName
     */
    private function renderTemplate(RenderPass $renderPass, $templateName)
    {
        $template = $this->templateFactory->createFromName($renderPass, $templateName);
        if (! $template instanceof Template) {
            throw new \InvalidArgumentException(sprintf('The template "%s" could not be found', $templateName));
        }

        foreach ($template->getActions() as $action) {
            $this->commandBus->handle($action);
            $this->emitter->emit(new RenderActionCompleted($action));
        }
    }
}
