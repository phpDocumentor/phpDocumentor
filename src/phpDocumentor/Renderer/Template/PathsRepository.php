<?php

namespace phpDocumentor\Renderer\Template;

use phpDocumentor\Path;
use phpDocumentor\Renderer\Template;

final class PathsRepository
{
    /** @var string */
    private $templateFolders = [];

    public function __construct(array $templateFolders = [])
    {
        $this->templateFolders = $templateFolders;
    }

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
}
