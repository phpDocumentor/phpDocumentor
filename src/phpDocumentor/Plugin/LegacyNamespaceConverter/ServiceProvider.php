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

namespace phpDocumentor\Plugin\LegacyNamespaceConverter;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Plugin\Plugin;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

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
class ServiceProvider implements ServiceProviderInterface
{

    /** @var Plugin */
    private $plugin;

    /**
     * Construct plugin with a the relevant configuration
     *
     * @param Plugin $plugin
     **/
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        $this->addNamespaceFilter($app['descriptor.builder'], $app['descriptor.filter']);
    }

    /**
     * Attaches the filter responsible for the conversion to all structural elements.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param Filter                   $filterManager
     *
     * @return void
     */
    private function addNamespaceFilter(ProjectDescriptorBuilder $builder, Filter $filterManager)
    {
        $filter = new LegacyNamespaceFilter($builder);

        // parse parameters
        foreach ($this->plugin->getParameters() as $param) {
            if ($param->getKey() == 'NamespacePrefix') {
                $filter->setNamespacePrefix($param->getValue());
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
