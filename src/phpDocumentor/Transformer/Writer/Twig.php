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
use phpDocumentor\Transformer\Router\ForFileProxy;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
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
final class Twig extends WriterAbstract
{
    private $environmentFactory;
    private $routers;

    public function __construct(EnvironmentFactory $environmentFactory, Queue $routers)
    {
        $this->environmentFactory = $environmentFactory;
        $this->routers = $routers;
    }

    /**
     * This method combines the ProjectDescriptor and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param ProjectDescriptor $project Document containing the structure.
     * @param Transformation $transformation Transformation to execute.
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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

            $destination = $this->routers->destination($node, $transformation);
            if ($destination === null) {
                continue;
            }

            $environment = $this->environmentFactory->create($project, $transformation, $destination);
            $environment->addGlobal('node', $node);

            $html = $environment->render(substr($transformation->getSource(), strlen($template_path)));
            file_put_contents($destination, $html);
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
