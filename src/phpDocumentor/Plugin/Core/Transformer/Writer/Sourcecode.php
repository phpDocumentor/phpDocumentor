<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Transformer\Writer;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * Sourcecode transformation writer; generates syntax highlighted source files in a destination's subfolder.
 */
class Sourcecode extends WriterAbstract
{
    /**
     * This method writes every source code entry in the structure file to a highlighted file.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     *
     * @return void
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation)
    {
        $artifact = $transformation->getTransformer()->getTarget()
            . DIRECTORY_SEPARATOR
            . ($transformation->getArtifact()
                ? $transformation->getArtifact()
                : 'source');

        /** @var FileDescriptor $file */
        foreach ($project->getFiles() as $file) {
            $filename = $file->getPath();
            $source   = $file->getSource();

            $root = str_repeat('../', count(explode(DIRECTORY_SEPARATOR, $filename)));
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
             SyntaxHighlighter.all();
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
