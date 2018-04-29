<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Cilex\Application;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\Descriptor\Filter\StripIgnore;
use phpDocumentor\Descriptor\Filter\StripInternal;
use phpDocumentor\Descriptor\Filter\StripOnVisibility;
use phpDocumentor\Reflection\DocBlock\Tags\Link;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\DocBlock\Tags\Version;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * This provider is responsible for registering the Descriptor component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Adds the services needed to build the descriptors.
     *
     * @param Container $app An Application instance
     */
    public function register(Container $app)
    {

        // I would prefer to extend it but due to a circular reference will pimple fatal
        $this->attachFiltersToManager($app['descriptor.filter'], $app);
    }

    /**
     * Attaches filters to the manager.
     *
     * @return Filter
     */
    public function attachFiltersToManager(Filter $filterManager, Application $app)
    {
        $stripOnVisibility = new StripOnVisibility($app['descriptor.builder']);
        $filtersOnAllDescriptors = [
            new StripInternal($app['descriptor.builder']),
            new StripIgnore($app['descriptor.builder']),
        ];

        foreach ($filtersOnAllDescriptors as $filter) {
            $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
            $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $filter);
        }

        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $stripOnVisibility);
        $filterManager->attach('phpDocumentor\Descriptor\MethodDescriptor', $stripOnVisibility);

        return $filterManager;
    }
}
