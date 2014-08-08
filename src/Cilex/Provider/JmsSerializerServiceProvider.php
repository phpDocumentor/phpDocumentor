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

namespace Cilex\Provider;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use JMS\Serializer\SerializerBuilder;

/**
 * Cilex Service Provider to provide serialization services.
 */
class JmsSerializerServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $vendorPath = isset($app['composer.vendor_path'])
            ? $app['composer.vendor_path']
            : __DIR__ . '/../../../vendor';

        $serializerPath = $vendorPath . '/jms/serializer/src';
        if (!file_exists($serializerPath)) {
            $serializerPath = __DIR__ . '/../../../../../jms/serializer/src';
        }

        $app['serializer.annotations'] = array(
            array('namespace' => 'JMS\Serializer\Annotation', 'path' => $serializerPath)
        );

        $app['serializer'] = $app->share(
            function ($container) {
                if (!isset($container['serializer.annotations']) || !is_array($container['serializer.annotations'])) {
                    throw new \RuntimeException(
                        'Expected the container to have an array called "serializer.annotations" that describes which '
                        . 'annotations are supported by the Serializer and where it can find them'
                    );
                }

                foreach ($container['serializer.annotations'] as $annotationsDefinition) {
                    if (!isset($annotationsDefinition['namespace'])) {
                        throw new \UnexpectedValueException(
                            'The annotation definition for the Serializer should have a key "namespace" that tells the '
                            . 'serializer what the namespace for the provided annotations are.'
                        );
                    }
                    if (!isset($annotationsDefinition['path'])) {
                        throw new \UnexpectedValueException(
                            'The annotation definition for the Serializer should have a key "path" that tells the '
                            . 'serializer where it can find the provided annotations.'
                        );
                    }

                    AnnotationRegistry::registerAutoloadNamespace(
                        $annotationsDefinition['namespace'],
                        $annotationsDefinition['path']
                    );
                }

                return SerializerBuilder::create()->build();
            }
        );
    }
}
