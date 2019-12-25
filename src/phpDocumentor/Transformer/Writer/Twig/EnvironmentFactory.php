<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use function ltrim;

class EnvironmentFactory
{
    /** @var LinkRenderer */
    private $renderer;

    public function __construct(LinkRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function create(
        ProjectDescriptor $project,
        Transformation $transformation,
        string $destination
    ) : Environment {
        $mountManager = $transformation->template()->files();
        $env = new Environment(
            new ChainLoader(
                [
                    new FlySystemLoader($mountManager->getFilesystem('template')),
                    new FlySystemLoader($mountManager->getFilesystem('templates')),
                ]
            )
        );

        $this->addPhpDocumentorExtension($project, $destination, $env);
        $this->enableDebugWhenParameterIsSet($transformation, $env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     */
    private function addPhpDocumentorExtension(
        ProjectDescriptor $project,
        string $path,
        Environment $twigEnvironment
    ) : void {
        $extension = new Extension($project, $this->renderer);
        $extension->setDestination(ltrim($path, '/\\'));
        $twigEnvironment->addExtension($extension);
    }

    private function enableDebugWhenParameterIsSet(Transformation $transformation, Environment $twigEnvironment) : void
    {
        $debugParameter = $transformation->getParameter('twig-debug');
        $isDebug = $debugParameter ? $debugParameter->value() : false;
        if ($isDebug !== 'true') {
            return;
        }

        $twigEnvironment->enableDebug();
        $twigEnvironment->enableAutoReload();
        $twigEnvironment->addExtension(new DebugExtension());
    }
}
