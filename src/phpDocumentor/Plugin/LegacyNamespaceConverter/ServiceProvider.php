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
use phpDocumentor\Plugin\LegacyNamespaceConverter\LegacyNamespaceFilter;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        $filterManager = $app['descriptor.filter'];
        $builder = $app['descriptor.builder'];

        $this->addNamespaceFilter($builder, $filterManager);
    }

    /**
     * @param $builder
     * @param $filterManager
     */
    private function addNamespaceFilter($builder, $filterManager)
    {
        $filter = new LegacyNamespaceFilter($builder);

        $filterManager->attach('phpDocumentor\Descriptor\ConstantDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\FunctionDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\InterfaceDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\TraitDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\PropertyDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\FileDescriptor', $filter);
        $filterManager->attach('phpDocumentor\Descriptor\ClassDescriptor', $filter);
    }


}
