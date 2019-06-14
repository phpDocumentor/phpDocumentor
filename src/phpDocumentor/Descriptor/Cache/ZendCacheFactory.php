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

namespace phpDocumentor\Descriptor\Cache;

use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\Cache\Storage\Plugin\PluginOptions;
use Zend\Cache\Storage\Plugin\Serializer as SerializerPlugin;

final class ZendCacheFactory
{
    public static function create()
    {
        $cache = new Filesystem();
        $cache->setOptions(
            [
                'namespace' => 'phpdoc-cache',
                'cache_dir' => sys_get_temp_dir(),
            ]
        );
        $plugin = new SerializerPlugin();

        if (extension_loaded('igbinary')) {
            $options = new PluginOptions();
            $options->setSerializer('igbinary');

            $plugin->setOptions($options);
        }

        $cache->addPlugin($plugin);

        return $cache;
    }
}
