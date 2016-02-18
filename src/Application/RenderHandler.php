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

namespace phpDocumentor\Application;

use League\Event\Emitter;
use League\Event\EmitterInterface;
use League\Flysystem\Filesystem;
use League\Tactician\CommandBus;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\Application\Render;
use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\ReadModel\ReadModels;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\Infrastructure\FileSystemFactory;
use phpDocumentor\DomainModel\Path;
use phpDocumentor\DomainModel\Renderer\Template;
use phpDocumentor\DomainModel\Renderer\TemplateFactory;
use phpDocumentor\DomainModel\Renderer\RenderContext;
use phpDocumentor\DomainModel\Renderer\RenderActionCompleted;
use phpDocumentor\DomainModel\Renderer\RenderingFinished;
use phpDocumentor\DomainModel\Renderer\RenderingStarted;
use phpDocumentor\Infrastructure\Renderer\FlySystemArtefacts;
use phpDocumentor\Infrastructure\Renderer\FlySystemAssets;

final class RenderHandler
{
    /** @var TemplateFactory */
    private $templateFactory;

    /** @var CommandBus */
    private $commandBus;

    /** @var FileSystemFactory */
    private $filesystemFactory;

    /** @var EmitterInterface */
    private $emitter;

    /** @var Assets */
    private $assets;

    /**
     * RenderHandler constructor.
     *
     * @param TemplateFactory $templateFactory
     * @param CommandBus $commandBus
     * @param FileSystemFactory $filesystemFactory
     * @param EmitterInterface $emitter
     * @param Assets $assets
     */
    public function __construct(
        TemplateFactory   $templateFactory,
        CommandBus        $commandBus,
        FileSystemFactory $filesystemFactory,
        EmitterInterface  $emitter,
        Assets            $assets
    ) {
        $this->templateFactory   = $templateFactory;
        $this->commandBus        = $commandBus;
        $this->filesystemFactory = $filesystemFactory;
        $this->emitter           = $emitter;
        $this->assets            = $assets;
    }

    public function __invoke(Render $command)
    {
        $this->emitter->emit(new RenderingStarted());

        $this->renderTemplates(
            $command->getTemplates(),
            new RenderContext(
                $this->createReadModels($command->getDocumentation()),
                $this->assets,
                $this->createArtefactsLocation($command->getTarget())
            )
        );

        $this->emitter->emit(new RenderingFinished());
    }

    /**
     * @param array $templates
     * @param RenderContext $renderContext
     */
    private function renderTemplates(array $templates, RenderContext $renderContext)
    {
        foreach ($templates as $templateDefinition) {
            $this->renderTemplate($templateDefinition, $renderContext);
        }
    }

    /**
     * @param array         $templateDefinition
     * @param RenderContext $renderContext
     */
    private function renderTemplate(array $templateDefinition, RenderContext $renderContext)
    {
        $template = $this->getTemplateFromDefinition($templateDefinition, $renderContext);

        $this->renderActions($template->getActions());
    }

    /**
     * @param array $templateDefinition
     * @param RenderContext $renderContext
     *
     * @return Template
     */
    private function getTemplateFromDefinition(array $templateDefinition, RenderContext $renderContext)
    {
        $template = null;
        if (isset($templateDefinition['name'])) {
            $template = $this->templateFactory->createFromName($renderContext, $templateDefinition['name']);

            if (! $template instanceof Template) {
                throw new \InvalidArgumentException(
                    sprintf('The template "%s" could not be found', $templateDefinition['name'])
                );
            }
        } elseif (isset($templateDefinition['path'])) {
            $template = $this->templateFactory->createFromUri($renderContext, new Uri($templateDefinition['path']));

            if (! $template instanceof Template) {
                throw new \InvalidArgumentException(
                    sprintf('The template at location "%s" could not be found', $templateDefinition['path'])
                );
            }
        }

        if (! $template instanceof Template) {
            throw new \InvalidArgumentException('No template could not be found');
        }

        return $template;
    }

    /**
     * @param Action[] $actions
     */
    private function renderActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->renderAction($action);
        }
    }

    /**
     * @param Action $action
     */
    private function renderAction(Action $action)
    {
        $this->commandBus->handle($action);
        $this->emitter->emit(new RenderActionCompleted($action));
    }

    /**
     * @param Documentation $documentation
     *
     * @return ReadModels
     */
    private function createReadModels(Documentation $documentation)
    {
        // TODO: Convert to a series of ReadModels
        return new ReadModels();
    }

    /**
     * @param Filesystem $destination
     *
     * @return FlySystemArtefacts
     */
    private function createArtefactsLocation(Filesystem $destination)
    {
        return new FlySystemArtefacts($destination);
    }
}
