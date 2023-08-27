<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser\Cache;

use phpDocumentor\Version;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\Traits\FilesystemTrait;

use function md5;
use function sprintf;
use function sys_get_temp_dir;

use const DIRECTORY_SEPARATOR;

final class FilesystemAdapter extends AbstractAdapter implements PruneableInterface
{
    use FilesystemTrait {
        init as doInit;
    }

    private const TTL_ONE_MONTH = 2_629_743;

    public function __construct(string $namespace = 'phpdoc', int $defaultLifetime = self::TTL_ONE_MONTH)
    {
        $this->marshaller = new DefaultMarshaller();

        parent::__construct($this->prefixNamespaceWithVersion($namespace), $defaultLifetime);

        $directory = sprintf('%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, 'phpdocumentor');

        $this->init($namespace, $directory);
    }

    /**
     * Sets up the caching folder with the given namespace.
     *
     * With phpDocumentor you can set the caching folder in your configuration; this poses an interesting problem with
     * the default FilesystemAdapter of Symfony as that only allows you to set the caching folder upon instantiation.
     *
     * This method prefixes the namespace with a hash of the current application version. We do this because changes
     * between versions, even minor ones, can cause unexpected issues due to serialization. As a trade-off, we thus
     * force a fresh cache if you use a new/different version of phpDocumentor.
     *
     * For example:
     *
     *     If a new property is added to a descriptor, upon unserialize PHP will attempt to populate that
     *     with the default property value, or fail if none is provided, but that default may not be the actual result.
     *
     *     Since phpDocumentor runs fast enough for the occasional cache clear, this will have limited impact for end
     *     users and guarantees that the cached state is correct.
     */
    public function init(string $namespace, string $directory): void
    {
        $prefixNamespaceWithVersion = $this->prefixNamespaceWithVersion($namespace);

        $this->doInit($prefixNamespaceWithVersion, $directory);
    }

    private function prefixNamespaceWithVersion(string $namespace): string
    {
        return sprintf('%s-%s', md5((new Version())->getVersion()), $namespace);
    }
}
