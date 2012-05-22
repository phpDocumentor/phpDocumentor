<?php
/**
 * Sourcecode Transformer File
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
 * Sourcecode transformation writer; generates syntax highlighted source files
 * in a destination's subfolder.
 *
 * @category   phpDocumentor
 * @package    Transformer
 * @subpackage Writers
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Sourcecode extends \phpDocumentor\Transformer\Writer\WriterAbstract
{
    /**
     * This method writes every source code entry in the structure file
     * to a highlighted file.
     *
     * @param \DOMDocument                        $structure      XML source.
     * @param \phpDocumentor\Transformer\Transformation $transformation Transformation.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function transform(\DOMDocument $structure,
        \phpDocumentor\Transformer\Transformation $transformation
    ) {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR
            . ($transformation->getArtifact()
                ? $transformation->getArtifact()
                : 'source');

        $xpath = new \DOMXPath($structure);
        $list = $xpath->query("/project/file[source]");

        for ($i=0; $i < $list->length; $i++) {
            /** @var \DOMElement $element */
            $element  = $list->item($i);
            $filename = $element->getAttribute('path');
            $source   = gzuncompress(
                base64_decode(
                    $element->getElementsByTagName('source')->item(0)->nodeValue
                )
            );

            $root = str_repeat(
                '../', count(explode(DIRECTORY_SEPARATOR, $filename))
            );
            $path = $artifact . DIRECTORY_SEPARATOR . $filename;
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            $source = htmlentities($source);
            file_put_contents(
                $path.'.html',
                <<<HTML
<html>
    <head>
        <script
            type="text/javascript"
            src="{$root}js/jquery-1.4.2.min.js">
        </script>
        <script
            type="text/javascript"
            src="{$root}syntax_highlighter/scripts/shCore.js">
        </script>
        <script
            type="text/javascript"
            src="{$root}syntax_highlighter/scripts/shBrushJScript.js">
        </script>
        <script
            type="text/javascript"
            src="{$root}syntax_highlighter/scripts/shBrushPhp.js">
        </script>
        <script
            type="text/javascript"
            src="{$root}syntax_highlighter/scripts/shBrushXml.js">
        </script>
        <link
            href="{$root}syntax_highlighter/styles/shCore.css" rel="stylesheet"
            type="text/css"
        />
        <link
            href="{$root}syntax_highlighter/styles/shCoreEclipse.css"
            rel="stylesheet" type="text/css"
        />
        <link
            href="{$root}syntax_highlighter/styles/shThemeWordpress.css"
            rel="stylesheet" type="text/css"
        />
    </head>
    <body>
        <pre class="brush: php">$source</pre>
        <script type="text/javascript">
             SyntaxHighlighter.all()
             jQuery('.gutter div').each(function(key, data){
                jQuery(data).prepend('<a name="L'+jQuery(data).text()+'"/>');
             });
        </script>
    </body>
</html>
HTML
            );
        }
    }
}