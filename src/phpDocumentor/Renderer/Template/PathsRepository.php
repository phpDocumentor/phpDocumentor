<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Renderer\Template;

use phpDocumentor\Path;
use phpDocumentor\Renderer\Template;

final class PathsRepository implements PathsRepositoryInterface
{
    /** @var string[] */
    private $templateFolders = [];

    /**
     * Initializes this repository with its dependencies.
     * @param string[] $templateFolders
     */
    public function __construct(array $templateFolders = [])
    {
        $this->templateFolders = $templateFolders;
    }

    /**
     * Lists the folders where templates can be found
     *
     * @param Template|null $template
     * @return string[]
     */
    public function listLocations(Template $template = null)
    {
        $templatesFolders = $this->templateFolders;

        // Determine the path of the current template and prepend it to the list so that it will always be queried
        // first.
        // http://twig.sensiolabs.org/doc/recipes.html#overriding-a-template-that-also-extends-itself
        if ($template) {
            $parameters = $template->getParameters();
            if (isset($parameters['directory'])
                && file_exists($parameters['directory']->getValue())
                && is_dir($parameters['directory']->getValue())
            ) {
                array_unshift($templatesFolders, $parameters['directory']->getValue());
            } elseif ($template->getName()) {
                foreach ($templatesFolders as $folder) {
                    $currentTemplatePath = $folder . '/' . $template->getName();
                    if (file_exists($currentTemplatePath)) {
                        array_unshift($templatesFolders, $currentTemplatePath);
                        break;
                    }
                }
            }
        }

        return $templatesFolders;
    }

    /**
     * Finds a template and returns the full name and path of the view
     *
     * @param Template $template
     * @param Path $view
     * @return null|Path
     */
    public function findByTemplateAndPath(Template $template, Path $view)
    {
        foreach ($this->listLocations($template) as $location) {
            $filename = (string)$location . '/' . (string)$view;
            if (file_exists($filename)) {
                if (!is_readable($filename)) {
                    $message = sprintf(
                        'File "%" for template "%s" was found but could not be read',
                        $filename,
                        $template->getName()
                    );

                    throw new \RuntimeException($message);
                }

                return new Path($filename);
            }
        }

        return null;
    }

    /**
     * Lists all available templates
     *
     * @return string[]
     */
    public function listTemplates()
    {
        $templates = [];

        foreach ($this->templateFolders as $templateFolder) {
            $subfolders = new \RecursiveDirectoryIterator($templateFolder);
            foreach ($subfolders as $subfolder) {
                if (file_exists($subfolder . '/template.xml')) {
                    $templates[] = basename($subfolder);
                }
            }
        }

        return $templates;
    }
}
