<?php

/*
 * This file is part of the Cilex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cilex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\DefaultTranslator;

/**
 * Symfony Validator component Provider.
 *
 * This class is an adaptation of the Silex MonologServiceProvider written by
 * Fabien Potencier.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Mike van Riel <mike.vanvriel@naenius.com>
 */
class ValidatorServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator'] = function ($app) {
            return new Validator(
                $app['validator.mapping.class_metadata_factory'],
                $app['validator.validator_factory'],
                $app['validator.default_translator']
            );
        };

        $app['validator.mapping.class_metadata_factory'] = function () {
            return new ClassMetadataFactory(new StaticMethodLoader());
        };

        $app['validator.validator_factory'] = function () {
            return new ConstraintValidatorFactory();
        };

        $app['validator.default_translator'] = function () {
            if (!class_exists('Symfony\\Component\\Validator\\DefaultTranslator')) {
                return array();
            }

            return new DefaultTranslator();
        };

        if (isset($app['validator.class_path'])) {
            $app['autoloader']->registerNamespace('Symfony\\Component\\Validator', $app['validator.class_path']);
        }
    }
}
