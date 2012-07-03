<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Twig\Transformer\Writer;

use \phpDocumentor\Transformer\Transformation;

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
        \DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $structure = simplexml_import_dom($structure);
        $destination_path = $this->getDestinationPath($transformation);

        if ($transformation->getQuery()) {
            $structure = $structure->xpath($transformation->getQuery());
        }

        if (!is_array($structure)) {
            $structure = array($structure);
        }

        $template_path = $this->getTemplatePath($transformation);

        foreach ($structure as $node) {
            $destination = preg_replace_callback(
                '/{([^}]+)}/u',
                function($query) use ($node) {
                    $name = ($query[1][0] === '@')
                        ? (string)$node[substr($query[1], 1)]
                        : (string)$node->{$query[1]};
                    return ltrim($name, '\\/'); // strip any preceding \ or /
                },
                $destination_path
            );

            $destination = str_replace('\\', DIRECTORY_SEPARATOR, $destination);
            if (!file_exists(dirname($destination))) {
                mkdir(dirname($destination), 0777, true);
            }

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

        /** @var \SimpleXMLElement $extension */
        foreach (
            (array)$transformation->getParameter('twig-extension', array())
            as $extension
        ) {
            $extension = (string)$extension;
            if (!class_exists($extension)) {
                throw new \InvalidArgumentException(
                    'Unknown twig extension: ' . $extension
                );
            }
            $extension_object = new $extension($structure, $transformation);
            $env->addExtension($extension_object);
        }
        return $env;
    }

    protected function getTemplatePath($transformation)
    {
        $parts = preg_split('[\\\\|/]', $transformation->getSource());
        $template_path = $parts[0] . DIRECTORY_SEPARATOR . $parts[1];
        return $template_path;
    }

    protected function getDestinationPath(Transformation $transformation)
    {
        return $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR . $transformation->getArtifact();
    }
}
