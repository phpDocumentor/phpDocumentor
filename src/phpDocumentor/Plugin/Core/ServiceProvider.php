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

namespace phpDocumentor\Plugin\Core;

use Cilex\Application;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use phpDocumentor\Translator\Translator;
use phpDocumentor\Plugin\Core\Transformer\Writer;
use phpDocumentor\Transformer\Writer\Collection;

/**
 * Register all services and subservices necessary to get phpDocumentor up and running.
 *
 * This provider exposes no services of its own but populates the Writer Collection with the basic writers for
 * phpDocumentor and, for backwards compatibility, registers the service providers for Graphs, Twig and PDF to
 * the container.
 */
final class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Container $app An Application instance.
     *
     * @return void
     */
    public function register(Container $app)
    {
        $this->registerTranslationMessages($app);
        $this->registerWriters($app);
        $this->registerDependenciesOnXsltExtension($app);

        $app->register(new \phpDocumentor\Plugin\Graphs\ServiceProvider());
        $app->register(new \phpDocumentor\Plugin\Twig\ServiceProvider());
    }

    /**
     * Creates all writers for this plugin and adds them to the WriterCollection object.
     *
     * This action will enable transformations in templates to make use of these writers.
     *
     * @param Container $container
     *
     * @return void
     */
    private function registerWriters(Container $container)
    {
        $writerCollection = $this->getWriterCollection($container);

        $writerCollection['FileIo'] = new Writer\FileIo();
        $writerCollection['checkstyle'] = new Writer\Checkstyle();
        $writerCollection['sourcecode'] = new Writer\Sourcecode();
        $writerCollection['statistics'] = new Writer\Statistics();
        $writerCollection['xml'] = new Writer\Xml($container['transformer.routing.standard']);
        $writerCollection['xsl'] = new Writer\Xsl($container['monolog']);

        $writerCollection['checkstyle']->setTranslator($this->getTranslator($container));
        $writerCollection['xml']->setTranslator($this->getTranslator($container));
    }

    /**
     * Registers the Messages folder in this plugin as a source of translations.
     *
     * @param Container $container
     *
     * @return void
     */
    private function registerTranslationMessages(Container $container)
    {
        $this->getTranslator($container)->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');
    }

    /**
     * Registers the Routing Queue and Descriptor Builder objects on the XSLT Extension class.
     *
     * In every template we use PHP helpers in order to be able to have routing that is universal between templates and
     * convert Markdown text into HTML (for example). The only way for XSL to do this is by having global functions or
     * static methods in a class because you cannot inject an object into an XSL processor.
     *
     * With this method we make sure that all dependencies used by the static methods are injected as static properties.
     *
     * @param Container $container
     *
     * @return void
     */
    private function registerDependenciesOnXsltExtension(Container $container)
    {
        Xslt\Extension::$routers = $container['transformer.routing.queue'];
        Xslt\Extension::$descriptorBuilder = $container['descriptor.builder'];
    }

    /**
     * Returns the Translator service from the Service Locator.
     *
     * @param Container $container
     *
     * @return Translator
     */
    private function getTranslator(Container $container)
    {
        return $container['translator'];
    }

    /**
     * Returns the WriterCollection service from the Service Locator.
     *
     * @param Container $container
     *
     * @return Collection
     */
    private function getWriterCollection(Container $container)
    {
        return $container['transformer.writer.collection'];
    }
}
