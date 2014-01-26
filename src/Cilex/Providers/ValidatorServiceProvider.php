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
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator;

/**
 * Cilex Service Provider to provide validation services.
 *
 * @link https://github.com/fabpot/Silex/blob/master/src/Silex/Provider/ValidatorServiceProvider.php Inspired by the
 *     Silex Service Provider, written by Fabien Potencier.
 */
class ValidatorServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['validator'] = $app->share(
            function ($app) {
                return new Validator(
                    $app['validator.mapping.class_metadata_factory'],
                    $app['validator.validator_factory'],
                    isset($app['translator']) ? $app['translator'] : new DefaultTranslator()
                );
            }
        );

        $app['validator.mapping.class_metadata_factory'] = $app->share(
            function () {
                return new ClassMetadataFactory(new StaticMethodLoader());
            }
        );

        $app['validator.validator_factory'] = $app->share(
            function () use ($app) {
                $validators = isset($app['validator.validator_service_ids'])
                    ? $app['validator.validator_service_ids']
                    : array();

                return new ConstraintValidatorFactory($app, $validators);
            }
        );
    }
}
