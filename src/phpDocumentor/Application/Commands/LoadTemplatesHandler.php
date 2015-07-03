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

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Input\InputInterface;

final class LoadTemplatesHandler
{
    /** @var Transformer */
    private $transformer;

    public function __construct(Transformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function __invoke(LoadTemplates $command)
    {
        $templates = $command->getConfiguration()->getTransformations()->getTemplates();
        foreach ($this->getTemplates($command->getTemplates(), $templates) as $template) {
            $this->transformer->getTemplates()->load($template, $this->transformer);
        }

        $transformations = $command->getConfiguration()->getTransformations()->getTransformations();
        $this->loadTransformations($this->transformer, $transformations);
    }

    /**
     * Retrieves the templates to be used by analyzing the options and the configuration.
     *
     * @param string[]   $providedTemplates
     * @param Template[] $templatesFromConfig
     *
     * @return \string[]
     */
    private function getTemplates(array $providedTemplates, array $templatesFromConfig)
    {
        $templates = $providedTemplates;
        if (!$templates) {
            foreach ($templatesFromConfig as $template) {
                $templates[] = $template->getName();
            }
        }

        if (!$templates) {
            $templates = array('clean');
        }

        return $templates;
    }

    /**
     * Load custom defined transformations.
     *
     * @param Transformer $transformer
     *
     * @todo this is an ugly implementation done for speed of development, should be refactored
     *
     * @return void
     */
    private function loadTransformations(Transformer $transformer, array $transformations)
    {
        $received = array();
        if (is_array($transformations)) {
            if (isset($transformations['writer'])) {
                $received[] = $this->createTransformation($transformations);
            } else {
                foreach ($transformations as $transformation) {
                    if (is_array($transformation)) {
                        $received[] = $this->createTransformation($transformations);
                    }
                }
            }
        }

        $this->appendReceivedTransformations($transformer, $received);
    }

    /**
     * Create Transformation instance.
     *
     * @param array $transformations
     *
     * @return \phpDocumentor\Transformer\Transformation
     */
    private function createTransformation(array $transformations)
    {
        return new Transformation(
            isset($transformations['query']) ? $transformations['query'] : '',
            $transformations['writer'],
            isset($transformations['source']) ? $transformations['source'] : '',
            isset($transformations['artifact']) ? $transformations['artifact'] : ''
        );
    }

    /**
     * Append received transformations.
     *
     * @param Transformer $transformer
     * @param array       $received
     *
     * @return void
     */
    private function appendReceivedTransformations(Transformer $transformer, $received)
    {
        if (!empty($received)) {
            $template = new Template('__');
            foreach ($received as $transformation) {
                $template[] = $transformation;
            }
            $transformer->getTemplates()->append($template);
        }
    }
}
