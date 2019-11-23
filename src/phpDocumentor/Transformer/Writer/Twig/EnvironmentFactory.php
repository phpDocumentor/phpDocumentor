<?php declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Router\Renderer;
use phpDocumentor\Transformer\Transformation;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;

final class EnvironmentFactory
{
    private $baseEnvironment;
    /**
     * @var Renderer
     */
    private $renderer;

    public function __construct(Environment $baseEnvironment, Renderer $renderer)
    {
        $this->baseEnvironment = $baseEnvironment;
        $this->renderer = $renderer;
    }

    public function create(
        ProjectDescriptor $project,
        Transformation $transformation,
        string $destination
    ): Environment {
        $callingTemplatePath = $this->getTemplatePath($transformation);

        $baseTemplatesPath = $transformation->getTransformer()->getTemplates()->getTemplatesPath();

        $templateFolders = [
            $baseTemplatesPath . '/..' . DIRECTORY_SEPARATOR . $callingTemplatePath,
            // http://twig.sensiolabs.org/doc/recipes.html#overriding-a-template-that-also-extends-itself
            $baseTemplatesPath,
        ];

        // get all invoked template paths, they overrule the calling template path
        /** @var \phpDocumentor\Transformer\Template $template */
        foreach ($transformation->getTransformer()->getTemplates() as $template) {
            $path = $baseTemplatesPath . DIRECTORY_SEPARATOR . $template->getName();
            array_unshift($templateFolders, $path);
        }

        // Clone twig because otherwise we cannot re-set the extensions on this twig environment on every run of this
        // writer
        $env = clone $this->baseEnvironment;
        $env->setLoader(new FilesystemLoader($templateFolders));

        $this->addPhpDocumentorExtension($project, $transformation, $destination, $env);
        $this->addExtensionsFromTemplateConfiguration($transformation, $project, $env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     */
    private function addPhpDocumentorExtension(
        ProjectDescriptor $project,
        Transformation $transformation,
        string $destination,
        Environment $twigEnvironment
    ): void {
        $base_extension = new Extension($project, $transformation, $this->renderer);
        $base_extension->setDestination(
            substr($destination, strlen($transformation->getTransformer()->getTarget()) + 1)
        );
        $twigEnvironment->addExtension($base_extension);
    }

    /**
     * Tries to add any custom extensions that have been defined in the template or the transformation's configuration.
     *
     * This method will read the `twig-extension` parameter of the transformation (which inherits the template's
     * parameter set) and try to add those extensions to the environment.
     *
     * @throws \InvalidArgumentException if a twig-extension should be loaded but it could not be found.
     */
    private function addExtensionsFromTemplateConfiguration(
        Transformation $transformation,
        ProjectDescriptor $project,
        Environment $twigEnvironment
    ): void {
        $isDebug = $transformation->getParameter('twig-debug')
            ? $transformation->getParameter('twig-debug')->getValue()
            : false;
        if ($isDebug === 'true') {
            $twigEnvironment->enableDebug();
            $twigEnvironment->enableAutoReload();
            $twigEnvironment->addExtension(new DebugExtension());
        }

        /** @var \phpDocumentor\Transformer\Template\Parameter $extension */
        foreach ($transformation->getParametersWithKey('twig-extension') as $extension) {
            $extensionValue = $extension->getValue();
            if (!class_exists($extensionValue)) {
                throw new \InvalidArgumentException('Unknown twig extension: ' . $extensionValue);
            }

            // to support 'normal' Twig extensions we check the interface to determine what instantiation to do.
            $implementsInterface = in_array(
                'phpDocumentor\Transformer\Writer\Twig\ExtensionInterface',
                class_implements($extensionValue),
                true
            );

            $twigEnvironment->addExtension(
                $implementsInterface ? new $extensionValue($project, $transformation) : new $extensionValue()
            );
        }
    }

    /**
     * Returns the path belonging to the template.
     */
    private function getTemplatePath(Transformation $transformation): string
    {
        $parts = preg_split('[\\\\|/]', $transformation->getSource());

        return $parts[0] . DIRECTORY_SEPARATOR . $parts[1];
    }
}
