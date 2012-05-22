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

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

/**
 * Search writer responsible for building the search index.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Search extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /**
     * Creates the search index at the artifact location.
     *
     * @param \DOMDocument                        $structure      Structure source
     *     use as basis for the transformation.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation
     *     that supplies the meta-data for this writer.
     *
     * @return void
     */
    public function transform(
        \DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $this->createXmlIndex(
            $structure,
            $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR
            . $transformation->getArtifact()
        );
    }

    /**
     * Helper method to create the actual index.
     *
     * @param \DOMDocument $xml         Structure source use as basis for
     *     the transformation.
     * @param string      $target_path The path where to generate the index.
     *
     * @todo refactor this method to be smaller and less complex.
     *
     * @return void
     */
    public function createXmlIndex(\DOMDocument $xml, $target_path)
    {
        $this->log('Generating the search index');

        $output = new \SimpleXMLElement('<nodes></nodes>');
        $xml = simplexml_import_dom($xml);

        foreach ($xml->file as $file) {
            foreach ($file->interface as $interface) {
                $interface_node = $output->addChild('node');
                $interface_node->value = (string)$interface->full_name;
                $interface_node->id = $file['generated-path'] . '#'
                    . $interface_node->value;
                $interface_node->type = 'interface';

                foreach ($interface->constant as $constant) {
                    $node = $output->addChild('node');
                    $node->value = (string)$interface->full_name . '::'
                        . (string)$constant->name;
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'constant';
                }

                foreach ($interface->property as $property) {
                    $node = $output->addChild('node');
                    $node->value = (string)$interface->full_name . '::'
                        . (string)$property->name;
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'property';
                }

                foreach ($interface->method as $method) {
                    $node = $output->addChild('node');
                    $node->value = (string)$interface->full_name . '::'
                        . (string)$method->name . '()';
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'method';
                }
            }

            foreach ($file->class as $class) {
                $class_node = $output->addChild('node');
                $class_node->value = (string)$class->full_name;
                $class_node->id = $file['generated-path'] . '#' . $class_node->value;
                $class_node->type = 'class';

                foreach ($class->constant as $constant) {
                    $node = $output->addChild('node');
                    $node->value = (string)$class->full_name . '::'
                        . (string)$constant->name;
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'constant';
                }

                foreach ($class->property as $property) {
                    $node = $output->addChild('node');
                    $node->value = (string)$class->full_name . '::'
                        . (string)$property->name;
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'property';
                }

                foreach ($class->method as $method) {
                    $node = $output->addChild('node');
                    $node->value = (string)$class->full_name . '::'
                        . (string)$method->name . '()';
                    $node->id = $file['generated-path'] . '#' . $node->value;
                    $node->type = 'method';
                }
            }

            foreach ($file->constant as $constant) {
                $node = $output->addChild('node');
                $node->value = (string)$constant->name;
                $node->id = $file['generated-path'] . '#::' . $node->value;
                $node->type = 'constant';
            }

            foreach ($file->function as $function) {
                $node = $output->addChild('node');
                $node->value = (string)$function->name . '()';
                $node->id = $file['generated-path'] . '#::' . $node->value;
                $node->type = 'function';
            }
        }

        $output->asXML($target_path . '/search_index.xml');
    }
}