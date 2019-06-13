<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core;

use Cilex\Application;
use phpDocumentor\Plugin\Core\Transformer\Writer;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Writer\Collection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

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
     */
    public function register(Container $app): void
    {
        $this->registerDependenciesOnXsltExtension($app);

        $app->register(new \phpDocumentor\Plugin\Twig\ServiceProvider());
    }

    /**
     * Registers the Routing Queue and Descriptor Builder objects on the XSLT Extension class.
     *
     * In every template we use PHP helpers in order to be able to have routing that is universal between templates and
     * convert Markdown text into HTML (for example). The only way for XSL to do this is by having global functions or
     * static methods in a class because you cannot inject an object into an XSL processor.
     *
     * With this method we make sure that all dependencies used by the static methods are injected as static properties.
     */
    private function registerDependenciesOnXsltExtension(Application $app)
    {
        $queue = new Queue();
        $queue->insert($app['transformer.routing.standard'], 1);
        Xslt\Extension::$routers = $queue;
        Xslt\Extension::$descriptorBuilder = $app['descriptor.builder'];
    }
}
