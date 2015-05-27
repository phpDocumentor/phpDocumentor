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

namespace phpDocumentor\Plugin\LegacyNamespaceConverter;

use phpDocumentor\Plugin\Parameter;
use phpDocumentor\Plugin\Plugin;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Analyzer;

/**
 * Converts all underscored class names into namespaces.
 *
 * This plugin will enable a non-namespaced application to be interpreted as being namespaced for documentation
 * purposes by separating the Classes by underscore and converting the prefix to a series of namespaces.
 *
 * For example:
 *
 *     `My_Special_ClassName` will be transformed into the class `ClassName` with namespace `My\Special`.
 *
 * @author david0 <https://github.com/david0> this plugin was generously provided by `@david0`.
 * @link   https://github.com/phpDocumentor/phpDocumentor2/pull/1135
 */
class ServiceProvider
{
    /**
     * @var Analyzer
     */
    private $analyzer;
    /**
     * @var Filter
     */
    private $filter;

    /**
     * Construct plugin with a the relevant configuration
     *
     * @param Plugin $plugin
     */
    public function __construct(Analyzer $analyzer, Filter $filter)
    {
        $this->analyzer = $analyzer;
        $this->filter   = $filter;
    }

    /**
     * Registers services on the given app.
     *
     * @param Parameter[] $options
     *
     * @return void
     */
    public function __invoke($options)
    {
        $this->addNamespaceFilter($this->analyzer, $this->filter, $options);
    }

    /**
     * Attaches the filter responsible for the conversion to all structural elements.
     *
     * @param Analyzer    $analyzer
     * @param Filter      $filterManager
     * @param Parameter[] $options
     *
     * @return void
     */
    private function addNamespaceFilter(Analyzer $analyzer, Filter $filterManager, array $options)
    {
        $filter = new LegacyNamespaceFilter($analyzer);

        // parse parameters
        foreach ($options as $option) {
            if ($option->getKey() == 'NamespacePrefix') {
                $filter->setNamespacePrefix($option->getValue());
            }
        }

        $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\FileDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\ClassDescriptor', $filter);
    }
}
