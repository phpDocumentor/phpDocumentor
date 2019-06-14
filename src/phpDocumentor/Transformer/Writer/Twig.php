<?php
declare(strict_types=1);

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

namespace phpDocumentor\Transformer\Writer;

use InvalidArgumentException;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Writer\Twig\Extension;
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Transformation;
use Twig_Environment;
use Twig_Extension_Debug;
use Twig_Loader_Filesystem;
use UnexpectedValueException;

/**
 * A specialized writer which uses the Twig templating engine to convert
 * templates to HTML output.
 *
 * This writer support the Query attribute of a Transformation to generate
 * multiple templates in one transformation.
 *
 * The Query attribute supports a simplified version of Twig queries and will
 * use each individual result as the 'node' global variable in the Twig template.
 *
 * Example:
 *
 *   Suppose a Query `indexes.classes` is given then this writer will be
 *   invoked as many times as there are classes in the project and the
 *   'node' global variable in twig will be filled with each individual
 *   class entry.
 *
 * When using the Query attribute in the transformation it is important to
 * use a variable in the Artifact attribute as well (otherwise the same file
 * would be overwritten several times).
 *
 * A simple example transformation line could be:
 *
 *     ```
 *     <transformation
 *         writer="twig"
 *         source="templates/twig/index.twig"
 *         artifact="index.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     index.twig template file in the twig template folder into index.html at
 *     the destination location.
 *     Since no Query is provided the 'node' global variable will contain
 *     the Project Descriptor of the Object Graph.
 *
 * A complex example transformation line could be:
 *
 *     ```
 *     <transformation
 *         query="indexes.classes"
 *         writer="twig"
 *         source="templates/twig/class.twig"
 *         artifact="{{name}}.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     class.twig template file in the twig template folder into a file with
 *     the 'name' property for an individual class inside the Object Graph.
 *     Since a Query *is* provided will the 'node' global variable contain a
 *     specific instance of a class applicable to the current iteration.
 *
 * @see self::getDestinationPath() for more information about variables in the
 *     Artifact attribute.
 */
class Twig extends WriterAbstract implements Routable
{
    /** @var Queue $routers */
    protected $routers;

    /** @var Twig_Environment $twig */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * This method combines the ProjectDescriptor and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation): void
    {
        $template_path = $this->getTemplatePath($transformation);

        $finder = new Pathfinder();
        $nodes = $finder->find($project, $transformation->getQuery());

        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }

            $destination = $this->getDestinationPath($node, $transformation);
            if ($destination === false) {
                continue;
            }

            $environment = $this->initializeEnvironment($project, $transformation, $destination);
            $environment->addGlobal('node', $node);

            $html = $environment->render(substr($transformation->getSource(), strlen($template_path)));
            file_put_contents($destination, $html);
        }
    }

    /**
     * Initializes the Twig environment with the template, base extension and additionally defined extensions.
     */
    protected function initializeEnvironment(
        ProjectDescriptor $project,
        Transformation $transformation,
        string $destination
    ): Twig_Environment {
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
        $env = clone $this->twig;
        $env->setLoader(new Twig_Loader_Filesystem($templateFolders));

        $this->addPhpDocumentorExtension($project, $transformation, $destination, $env);
        $this->addExtensionsFromTemplateConfiguration($transformation, $project, $env);

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     */
    protected function addPhpDocumentorExtension(
        ProjectDescriptor $project,
        Transformation $transformation,
        string $destination,
        Twig_Environment $twigEnvironment
    ): void {
        $base_extension = new Extension($project, $transformation);
        $base_extension->setDestination(
            substr($destination, strlen($transformation->getTransformer()->getTarget()) + 1)
        );
        $base_extension->setRouters($this->routers);
        $twigEnvironment->addExtension($base_extension);
    }

    /**
     * Tries to add any custom extensions that have been defined in the template or the transformation's configuration.
     *
     * This method will read the `twig-extension` parameter of the transformation (which inherits the template's
     * parameter set) and try to add those extensions to the environment.
     *
     * @throws InvalidArgumentException if a twig-extension should be loaded but it could not be found.
     */
    protected function addExtensionsFromTemplateConfiguration(
        Transformation $transformation,
        ProjectDescriptor $project,
        Twig_Environment $twigEnvironment
    ): void {
        $isDebug = $transformation->getParameter('twig-debug')
            ? $transformation->getParameter('twig-debug')->getValue()
            : false;
        if ($isDebug === 'true') {
            $twigEnvironment->enableDebug();
            $twigEnvironment->enableAutoReload();
            $twigEnvironment->addExtension(new Twig_Extension_Debug());
        }

        /** @var \phpDocumentor\Transformer\Template\Parameter $extension */
        foreach ($transformation->getParametersWithKey('twig-extension') as $extension) {
            $extensionValue = $extension->getValue();
            if (!class_exists($extensionValue)) {
                throw new InvalidArgumentException('Unknown twig extension: ' . $extensionValue);
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
     * Uses the currently selected node and transformation to assemble the destination path for the file.
     *
     * The Twig writer accepts the use of a Query to be able to generate output for multiple objects using the same
     * template.
     *
     * The given node is the result of such a query, or if no query given the selected element, and the transformation
     * contains the destination file.
     *
     * Since it is important to be able to generate a unique name per element can the user provide a template variable
     * in the name of the file.
     * Such a template variable always resides between double braces and tries to take the node value of a given
     * query string.
     *
     * Example:
     *
     *   An artifact stating `classes/{{name}}.html` will try to find the
     *   node 'name' as a child of the given $node and use that value instead.
     *
     * @param DescriptorAbstract|ProjectDescriptor $node
     * @throws InvalidArgumentException if no artifact is provided and no routing rule matches.
     * @throws UnexpectedValueException if the provided node does not contain anything.
     * @return false|string returns the destination location or false if generation should be aborted.
     */
    protected function getDestinationPath($node, Transformation $transformation)
    {
        $writer = $this;

        if (!$node) {
            throw new UnexpectedValueException(
                'The transformation node in the twig writer is not expected to be false or null'
            );
        }

        if (!$transformation->getArtifact()) {
            $rule = $this->routers->match($node);
            if (!$rule) {
                throw new InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . ($node === null ? 'NULL' : get_class($node))
                );
            }

            $rule = new ForFileProxy($rule);
            $url = $rule->generate($node);
            if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                return false;
            }

            $path = $transformation->getTransformer()->getTarget() . str_replace('/', DIRECTORY_SEPARATOR, $url);
        } else {
            $path = $transformation->getTransformer()->getTarget()
                . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        }

        $finder = new Pathfinder();
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function ($query) use ($node, $finder) {
                // strip any surrounding \ or /
                $filepart = trim((string) current($finder->find($node, $query[1])), '\\/');

                // make it windows proof
                if (extension_loaded('iconv')) {
                    $filepart = iconv('UTF-8', 'ASCII//TRANSLIT', $filepart);
                }

                return strpos($filepart, '/') !== false
                    ? implode('/', array_map('urlencode', explode('/', $filepart)))
                    : implode('\\', array_map('urlencode', explode('\\', $filepart)));
            },
            $path
        );

        // replace any \ with the directory separator to be compatible with the
        // current filesystem and allow the next file_exists to do its work
        $destination = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destination);

        // create directory if it does not exist yet
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }

    /**
     * Returns the path belonging to the template.
     */
    protected function getTemplatePath(Transformation $transformation): string
    {
        $parts = preg_split('[\\\\|/]', $transformation->getSource());

        return $parts[0] . DIRECTORY_SEPARATOR . $parts[1];
    }

    public function setRouters(Queue $routers): void
    {
        $this->routers = $routers;
    }
}
