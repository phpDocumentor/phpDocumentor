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

namespace phpDocumentor\Plugin\Twig;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Translator\Translator;
use phpDocumentor\Transformer\Writer\Collection;

/**
 * Provides a series of services that are necessary for Twig to work with phpDocumentor.
 *
 * This provider uses the translator component to fuel the twig writer and ands the to the twig writer to the writer
 * collection. This enables transformations to mention 'twig' as their writer attribute.
 *
 * @see Writer\Twig for more information on using this.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /** @var \DI\Container */
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        /** @var Translator $translator  */
        $translator = $this->container->get(Translator::class);

        /** @var Collection $writerCollection */
        $writerCollection = $this->container->get(Collection::class);

        $writerCollection['twig'] = new Writer\Twig();
        $writerCollection['twig']->setTranslator($translator);
    }
}
