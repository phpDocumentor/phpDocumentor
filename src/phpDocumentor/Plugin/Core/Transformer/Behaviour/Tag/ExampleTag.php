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

namespace phpDocumentor\Plugin\Core\Transformer\Behaviour\Tag;

/**
 * Behaviour that adds support for the example tag
 */
class ExampleTag
{
    /**
     * @var string $sourceDir
     */
    protected $sourceDir;

    /**
     * @var string $exampleDir
     */
    protected $exampleDir;

    /**
     * Find all example inline-tags inside the long-description.
     *
     * @param \DOMDocument $xml Structure source to apply behaviour onto.
     *
     * @return \DOMDocument
     */
    public function process(\DOMDocument $xml)
    {
        $exampleQry = '//long-description[contains(., "{@example")]';

        $xpath = new \DOMXPath($xml);
        $nodes = $xpath->query($exampleQry);

        /** @var \DOMElement $node */
        foreach ($nodes as $node) {

            preg_match('/\{@example\s(.+?)\}/', $node->nodeValue, $paramString);
            $params = explode(' ', $paramString[1]);

            $filename = $params[0];
            $offset = isset($params[1]) && is_numeric($params[1]) ? (int) $params[1] : 1;
            $length = isset($params[2]) && is_numeric($params[2]) ? (int) $params[2] : null;

            $example = getcwd().DIRECTORY_SEPARATOR.'examples'.DIRECTORY_SEPARATOR.$filename;
            $exampleFromConfg = rtrim($this->exampleDir, '\\/') .DIRECTORY_SEPARATOR . $filename;
            $source = sprintf('%s%s%s%s%s', getcwd(), DIRECTORY_SEPARATOR, trim($this->sourceDir, '\\/'), DIRECTORY_SEPARATOR, trim($filename, '"'));

            $file = @file($exampleFromConfg) ?: @file($source) ?: @file($example) ?: @file($filename);

            if (is_array($file)) {
                $filepart = array_slice($file, $offset, $length);
                $content = implode('', $filepart);
            } else {
                $content = "** File not found : {$filename} ** ";
            }

            $node->nodeValue = preg_replace('/\{@example\s(.+?)\}/', '<pre>'.$content.'</pre>', $node->nodeValue);
        }

        return $xml;
    }

    /**
     * Set source directory.
     *
     * @param string $sourceDir
     *
     * @return void
     */
    public function setSourceDirectory($sourceDir)
    {
        $this->sourceDir = $sourceDir;
    }

    /**
     * Set example directory.
     *
     * @param string $exampleDir
     *
     * @return void
     */
    public function setExampleDirectory($exampleDir)
    {
        $this->exampleDir = $exampleDir;
    }
}
