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

use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Marshaller\DefaultMarshaller;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\Traits\FilesystemTrait;
use function sys_get_temp_dir;

final class FilesystemAdapter extends AbstractAdapter implements PruneableInterface
{
    use FilesystemTrait {
        init as doInit;
    }

    private const TTL_ONE_YEAR = 31556926;

    public function __construct(string $namespace = 'phpdoc', int $defaultLifetime = self::TTL_ONE_YEAR)
    {
        $this->marshaller = new DefaultMarshaller();

        parent::__construct($namespace, $defaultLifetime);

        $this->init($namespace, sys_get_temp_dir() . '/phpdocumentor');
    }

    /**
     * Sets up the caching folder with the given namespace.
     *
     * With phpDocumentor you can set the caching folder in your configuration; this poses an interesting problem with
     * the default FilesystemAdapter of Symfony as that only allows you to set the caching folder upon instantiation.
     */
    public function init(string $namespace, string $directory) : void
    {
        $this->doInit($namespace, $directory);
    }
}
