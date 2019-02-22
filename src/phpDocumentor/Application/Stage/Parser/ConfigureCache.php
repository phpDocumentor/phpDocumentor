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

namespace phpDocumentor\Application\Stage\Parser;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Zend\Cache\Storage\StorageInterface;

final class ConfigureCache
{
    /**
     * @var StorageInterface
     */
    private $cache;

    public function __construct(StorageInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws RuntimeException if the target location is not a folder.
     */
    public function __invoke(array $configuration): array
    {
        $target = (string) $configuration['phpdocumentor']['paths']['cache'];

        //Process cache setup
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath($target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }
        if (!file_exists($target)) {
            if (!mkdir($target, 0755, true) && !is_dir($target)) {
                throw new RuntimeException('The provided target location must be a directory');
            }
        }

        $this->cache->getOptions()->setCacheDir($target);

        return $configuration;
    }
}
