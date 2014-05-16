<?php

namespace phpDocumentor\Translator;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Configuration as ApplicationConfiguration;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        /** @var ApplicationConfiguration $config */
        $config = $app['config'];

        $app['translator.locale'] = $config->getTranslator()->getLocale();

        $app['translator'] = $app->share(
            function ($app) {
                $translator = new Translator();
                $translator->setLocale($app['translator.locale']);

                return $translator;
            }
        );
    }
} 