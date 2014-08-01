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
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        /** @var Translator $translator  */
        $translator = $app['translator'];

        /** @var Collection $writerCollection */
        $writerCollection = $app['transformer.writer.collection'];

        $writerCollection['twig'] = new Writer\Twig();
        $writerCollection['twig']->setTranslator($translator);
    }
}
