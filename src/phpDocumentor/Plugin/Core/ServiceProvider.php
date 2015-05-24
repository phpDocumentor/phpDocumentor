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

namespace phpDocumentor\Plugin\Core;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Router\StandardRouter;
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
    /** @var \DI\Container */
    private $container;

    public function __construct($plugin, $container)
    {
        $this->container = $container;
    }

    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->registerTranslationMessages($app);
        $this->registerWriters($app);
        $this->registerDependenciesOnXsltExtension($app);

        $app->register(new \phpDocumentor\Plugin\Graphs\ServiceProvider($this->container));
        $app->register(new \phpDocumentor\Plugin\Twig\ServiceProvider($this->container));
    }

    /**
     * Creates all writers for this plugin and adds them to the WriterCollection object.
     *
     * This action will enable transformations in templates to make use of these writers.
     *
     * @param Application $app
     *
     * @return void
     */
    private function registerWriters(Application $app)
    {
        $writerCollection = $this->getWriterCollection($app);

        $writerCollection['FileIo'] = new Writer\FileIo();
        $writerCollection['checkstyle'] = new Writer\Checkstyle();
        $writerCollection['sourcecode'] = new Writer\Sourcecode();
        $writerCollection['statistics'] = new Writer\Statistics();
        $writerCollection['xml'] = new Writer\Xml($this->container->get(StandardRouter::class));
        $writerCollection['xsl'] = new Writer\Xsl();
        $writerCollection['jsonp'] = new Writer\Jsonp();

        $writerCollection['checkstyle']->setTranslator($this->getTranslator());
        $writerCollection['xml']->setTranslator($this->getTranslator());
    }

    /**
     * Registers the Messages folder in this plugin as a source of translations.
     *
     * @param Application $app
     *
     * @return void
     */
    private function registerTranslationMessages(Application $app)
    {
        $this->getTranslator()->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');
    }

    /**
     * Registers the Routing Queue and Descriptor Analyzer objects on the XSLT Extension class.
     *
     * In every template we use PHP helpers in order to be able to have routing that is universal between templates and
     * convert Markdown text into HTML (for example). The only way for XSL to do this is by having global functions or
     * static methods in a class because you cannot inject an object into an XSL processor.
     *
     * With this method we make sure that all dependencies used by the static methods are injected as static properties.
     *
     * @param Application $app
     *
     * @return void
     */
    private function registerDependenciesOnXsltExtension(Application $app)
    {
        Xslt\Extension::$routers = $this->container->get(Queue::class);
        Xslt\Extension::$analyzer = $this->container->get(Analyzer::class);
    }

    /**
     * Returns the Translator service from the Service Locator.
     *
     * @return Translator
     */
    private function getTranslator()
    {
        return $this->container->get(Translator::class);
    }

    /**
     * Returns the WriterCollection service from the Service Locator.
     *
     * @param Application $app
     *
     * @return Collection
     */
    private function getWriterCollection(Application $app)
    {
        return $this->container->get(Collection::class);
    }
}
