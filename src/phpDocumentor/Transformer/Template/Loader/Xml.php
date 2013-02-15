<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template\Loader;

use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;

class Xml
{
    /** @var Collection $writerCollection */
    protected $writerCollection;

    /** @var Transformer */
    protected $transformer;

    public function __construct(Transformer $transformer, WriterCollection $writerCollection)
    {
        $this->writerCollection = $writerCollection;
        $this->transformer = $transformer;
    }

    public function load(Template $template, $xml)
    {
        $xml = new \SimpleXMLElement($xml);
        $template->setAuthor((string)$xml->author);
        $template->setVersion((string)$xml->version);
        $template->setCopyright($xml->copyright);

        foreach ($xml->transformations->transformation as $index => $transformation) {
            $transformation_obj = new Transformation(
                $this->transformer,
                (string)$transformation['query'],
                $this->writerCollection[(string)$transformation['writer']],
                (string)$transformation['source'],
                (string)$transformation['artifact']
            );

            // import generic parameters of the template
            if (isset($xml->parameters) && count($xml->parameters)
            ) {
                $transformation_obj->importParameters($xml->parameters);
            }

            if (isset($transformation->parameters)
                && count($transformation->parameters)
            ) {
                $transformation_obj->importParameters(
                    $transformation->parameters
                );
            }

            $template[] = $transformation_obj;
        }
    }
}