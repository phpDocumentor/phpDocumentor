<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Template\Action;

use phpDocumentor\DomainModel\Renderer\Template\Action;

/**
 * Sourcecode transformation writer; generates syntax highlighted source files in a destination's subfolder.
 */
class SourcecodeHandler
{
    private $analyzer;

    /**
     * Sourcecode constructor.
     */
    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * This method writes every source code entry in the structure file to a highlighted file.
     *
     * @param Sourcecode $action
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $artifact = $action->getRenderContext()->getDestination() . '/' . ltrim($action->getDestination(), '\\/');
        $project = $this->analyzer->getProjectDescriptor();

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

            $fs = $action->getRenderContext()->getFilesystem();
            $fs->put(
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
