<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Webmozart\Assert\Assert;

use function count;
use function ltrim;
use function preg_split;
use function strlen;
use function substr;

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
 *
 *
 * When using the Query attribute in the transformation it is important to
 * use a variable in the Artifact attribute as well (otherwise the same file
 * would be overwritten several times).
 *
 * A simple example transformation line could be:
 *
 * ```
 * <transformation
 *     writer="twig"
 *     source="templates/twig/index.twig"
 *     artifact="index.html"
 * />
 * ```
 *
 * This example transformation would use this writer to transform the
 * index.twig template file in the twig template folder into index.html at
 * the destination location.
 * Since no Query is provided the 'node' global variable will contain
 * the Project Descriptor of the Object Graph.
 *
 * A complex example transformation line could be:
 *
 * ```
 * <transformation
 *     query="indexes.classes"
 *     writer="twig"
 *     source="templates/twig/class.twig"
 *     artifact="{{name}}.html"/>
 * ```
 *
 * This example transformation would use this writer to transform the
 * class.twig template file in the twig template folder into a file with
 * the 'name' property for an individual class inside the Object Graph.
 * Since a Query *is* provided will the 'node' global variable contain a
 * specific instance of a class applicable to the current iteration.
 *
 * @see self::getDestinationPath() for more information about variables in the
 *     Artifact attribute.
 */
final class Twig extends WriterAbstract implements Initializable
{
    use IoTrait;

    /** @var EnvironmentFactory */
    private $environmentFactory;

    /** @var PathGenerator */
    private $pathGenerator;

    /** @var Environment */
    private $environment;

    public function __construct(
        EnvironmentFactory $environmentFactory,
        PathGenerator $pathGenerator
    ) {
        $this->environmentFactory = $environmentFactory;
        $this->pathGenerator = $pathGenerator;
    }

    public function initialize(ProjectDescriptor $project, Template $template): void
    {
        $this->environment = $this->environmentFactory->create($project, $template);
    }

    /**
     * This method combines the ProjectDescriptor and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation): void
    {
        $templatePath = $this->getTemplatePath($transformation);

        $finder = new Pathfinder();
        $nodes = $finder->find($project, $transformation->getQuery());

        foreach ($nodes as $node) {
            if (!$node) {
                continue;
            }

            $path = $this->pathGenerator->generate($node, $transformation);
            if ($path === '') {
                continue;
            }

            $this->environment->addGlobal('project', $project);
            $this->environment->addGlobal('usesNamespaces', count($project->getNamespace()->getChildren()) > 0);
            $this->environment->addGlobal('usesPackages', count($project->getPackage()->getChildren()) > 0);
            $this->environment->addGlobal('documentationSet', $project);
            $this->environment->addGlobal('node', $node);
            $this->environment->addGlobal('destinationPath', $path);
            $this->environment->addGlobal('parameter', $transformation->getParameters());

            $output = $this->environment->render(
                substr($transformation->getSource(), strlen($templatePath)),
                ['target_path' => ltrim($path, '/\\')]
            );

            $this->persistTo($transformation, ltrim($path, '/\\'), $output);
        }
    }

    /**
     * Returns the path belonging to the template.
     */
    private function getTemplatePath(Transformation $transformation): string
    {
        $parts = preg_split('~[\\\\|/]~', $transformation->getSource());

        Assert::isArray($parts);

        if ($parts[0] !== 'templates') {
            return '';
        }

        return $parts[0] . '/' . $parts[1];
    }
}
