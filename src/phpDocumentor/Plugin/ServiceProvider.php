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

namespace phpDocumentor\Plugin;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RuntimeException;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app): void
    {
        /** @var ApplicationConfiguration $config */
        $config = $app['config'];
        $plugins = []; //$config->getPlugins();

        if (! $plugins) {
            $app->register(new Core\ServiceProvider());
            $app->register(new Scrybe\ServiceProvider());

            return;
        }

        array_walk(
            $plugins,
            function ($plugin) use ($app) {
                /** @var Plugin $plugin */
                $provider = (strpos($plugin->getClassName(), '\\') === false)
                    ? sprintf('phpDocumentor\\Plugin\\%s\\ServiceProvider', $plugin->getClassName())
                    : $plugin->getClassName();
                if (!class_exists($provider)) {
                    throw new RuntimeException('Loading Service Provider for ' . $provider . ' failed.');
                }

                try {
                    $app->register(new $provider($plugin));
                } catch (\InvalidArgumentException $e) {
                    throw new RuntimeException($e->getMessage());
                }
            }
        );
    }
}
