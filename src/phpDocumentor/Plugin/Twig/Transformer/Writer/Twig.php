<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Twig\Transformer\Writer;

use \phpDocumentor\Transformer\Transformation;

/**
 * A specialized writer which uses the Twig templating engine to convert
 * templates to HTML output.
 *
 * This writer support the Query attribute of a Transformation to generate
 * multiple templates in one transformation.
 *
 * The Query attribute supports XPath queries and will use each individual
 * result as the 'ast_node' global variable in the Twig template.
 *
 * Example:
 *
 *   Suppose a Query `/project/file/class` is given then this writer will be
 *   invoked as many times as there are classes in the project and will the
 *   'ast_node' global variable in twig be filled with an individual class entry.
 *
 * When using the Query attribute in the transformation it is important to
 * use a variable in the Artefact attribute as well (otherwise the same result
 * file would be overwritten several times).
 *
 * A simple example transformation line could be:
 *
 *     ```
 *     <transformation
 *         writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
 *         source="templates/twig/index.twig"
 *         artifact="index.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     index.twig template file in the twig template folder into index.html at
 *     the destination location.
 *     Since no Query is provided will the 'ast_node' global variable contain the
 *     document root of the Abstract Syntax Tree, which is '/project'.
 *
 * A complex example transformation line could be:
 *
 *     ```
 *     <transformation
 *         query="/project/file/class|/project/file/interface"
 *         writer="\phpDocumentor\Plugin\Twig\Transformer\Writer\Twig"
 *         source="templates/twig/class.twig"
 *         artifact="{full_name}.html"/>
 *     ```
 *
 *     This example transformation would use this writer to transform the
 *     class.twig template file in the twig template folder into a file with
 *     the 'full_name' childnode for each individual class or interface inside
 *     the Abstract Syntax Tree.
 *     Since a Query *is* provided will the 'ast_node' global variable contain a
 *     specific instance of a class or interface applicable to the current
 *     iteration.
 *
 * @see getDestinationPath() for more information about variables in the
 *     Artefact attribute.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 */
class Twig extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /**
     * This method combines the structure.xml and the given target template
     * and creates a static html page at the artifact location.
     *
     * @param \DOMDocument                              $structure
     *     XML source.
     * @param \phpDocumentor\Transformer\Transformation $transformation
     *     Transformation.
     *
     * @return void
     */
    public function transform(
        \DOMDocument $structure, Transformation $transformation
    ) {
        $structure = simplexml_import_dom($structure);

        if ($transformation->getQuery()) {
            $structure = $structure->xpath($transformation->getQuery());
        }

        if (!is_array($structure)) {
            $structure = array($structure);
        }

        $template_path = $this->getTemplatePath($transformation);

        foreach ($structure as $node) {
            $destination = $this->getDestinationPath($node, $transformation);

            $this->log(
                'Processing the file: ' . $node->nodeValue
                . ' as ' . $destination
            );

            $environment = $this->initializeEnvironment(
                $node, $transformation, $destination
            );

            file_put_contents(
                $destination,
                $environment->render(
                    substr($transformation->getSource(), strlen($template_path))
                )
            );
        }
    }

    /**
     * Initializes the Twig environment with the template, base extension and
     * additionally defined extensions.
     *
     * @param \SimpleXMLElement $structure
     * @param Transformation    $transformation
     * @param string            $destination
     *
     * @return \Twig_Environment
     */
    protected function initializeEnvironment(
        \SimpleXMLElement $structure, Transformation $transformation,
        $destination
    ) {
        $template_path = $this->getTemplatePath($transformation);

        $env = new \Twig_Environment(
            new \Twig_Loader_Filesystem(
                $transformation->getTransformer()->getTemplatesPath().'/..'
                .DIRECTORY_SEPARATOR.$template_path
            )
        );

        $this->addPhpDocumentorExtension(
            $structure, $transformation, $destination, $env
        );
        $this->addExtensionsFromTemplateConfiguration(
            $transformation, $structure, $env
        );

        return $env;
    }

    /**
     * Adds the phpDocumentor base extension to the Twig Environment.
     *
     * @param \SimpleXMLElement $structure
     * @param Transformation    $transformation
     * @param string            $destination
     * @param \Twig_Environment $env
     *
     * @return void
     */
    protected function addPhpDocumentorExtension(
        $structure, $transformation, $destination, $env
    ) {
        $base_extension = new \phpDocumentor\Plugin\Twig\Extension(
            $structure, $transformation
        );
        $base_extension->setDestination(
            substr(
                $destination,
                strlen($transformation->getTransformer()->getTarget()) + 1
            )
        );
        $env->addExtension($base_extension);
    }

    /**
     * Tries to add any custom extensions that have been defined in the
     * template or the transformation's configuration.
     *
     * This method will read the `twig-extension` parameter of the
     * transformation (which inherits the template's parameter set) and
     * try to add those extensions to the environment.
     *
     * @param Transformation    $transformation
     * @param \SimpleXMLElement $structure
     * @param \Twig_Environment $env
     *
     * @throws \InvalidArgumentException if a twig-extension should be loaded
     *     but it could not be found.
     *
     * @return void
     */
    protected function addExtensionsFromTemplateConfiguration(
        $transformation, $structure, $env
    ) {
        /** @var \SimpleXMLElement $extension */
        foreach ((array)$transformation->getParameter('twig-extension', array())
            as $extension
        ) {
            $extension = (string)$extension;
            if (!class_exists($extension)) {
                throw new \InvalidArgumentException(
                    'Unknown twig extension: ' . $extension
                );
            }

            // to support 'normal' Twig extensions we check the interface
            // to determine what instantiation to do.
            $extension_object = (class_implements('ExtensionInterface'))
                ? new $extension($structure, $transformation)
                : new $extension();

            $env->addExtension($extension_object);
        }
    }

    /**
     * Uses the currently selected node and transformation to assemble the
     * destination path for the file.
     *
     * The Twig writer accepts the use of a Query to be able to generate output
     * for multiple objects using the same template.
     *
     * The given node is the result of such a query, or if no query given the
     * selected element, and the transformation contains the destination file.
     *
     * Since it is important to be able to generate a unique name per element
     * can the user provide a template variable in the name of the file.
     * Such a template variable always resides between braces and tries to
     * take the node value of a given node or attribute.
     *
     * Example:
     *
     *   An artefact stating `classes/{full_name}.html` will try to find the
     *   node 'full_name' as a child of the given $node and use that value
     *   instead.
     *
     *   An artefact stating `namespaces/{@full_name}.html` will try to find the
     *   attribute 'full_name' of the given $node and use that value instead.
     *
     * @param \SimpleXMLElement $node
     * @param Transformation    $transformation
     *
     * @return string
     */
    protected function getDestinationPath(
        \SimpleXMLElement $node, Transformation $transformation
    ) {
        $destination = preg_replace_callback(
            '/{([^}]+)}/u',
            function($query) use ($node) {
                // replace any variable with the value of a node or attribute
                // where a preceeding @ denotes the value of an attribute
                $name = ($query[1][0] === '@')
                    ? (string)$node[substr($query[1], 1)]
                    : (string)$node->{$query[1]};
                return ltrim($name, '\\/'); // strip any preceding \ or /
            },
            $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR
            . $transformation->getArtifact()
        );

        // replace any \ with the directory separator to be compatible with the
        // current filesystem and allow the next file_exists to do its work
        $destination = str_replace('\\', DIRECTORY_SEPARATOR, $destination);

        // create directory if it does not exist yet
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }

    /**
     * Returns the path belonging to the template.
     *
     * @param Transformation $transformation
     *
     * @return string
     */
    protected function getTemplatePath($transformation)
    {
        $parts = preg_split('[\\\\|/]', $transformation->getSource());
        $template_path = $parts[0] . DIRECTORY_SEPARATOR . $parts[1];
        return $template_path;
    }
}
