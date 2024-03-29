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

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\Cache\CacheInterface;
use Webmozart\Assert\Assert;

use function base64_decode;
use function base64_encode;
use function md5;
use function serialize;
use function unserialize;

final class CacheMiddleware implements Middleware
{
    public function __construct(private readonly CacheInterface $cache, private readonly LoggerInterface $logger)
    {
    }

    /**
     * Executes this middle ware class.
     * A middle ware class MUST return a File object or call the $next callable.
     *
     * @param callable(Command): File $next
     *
     * @throws InvalidArgumentException
     */
    public function execute(Command $command, callable $next): File
    {
        Assert::isInstanceOf($command, CreateCommand::class);

        $itemName = md5($command->getFile()->path());

        $cacheResponse = $this->cache->get(
            $itemName . '-' . $command->getFile()->md5(),
            function () use ($next, $command) {
                $this->logger->log(LogLevel::NOTICE, 'Parsing ' . $command->getFile()->path());
                $file = $next($command);

                return base64_encode(serialize($file));
            },
        );

        $unserializedObject = unserialize(base64_decode($cacheResponse));

        Assert::isInstanceOf($unserializedObject, File::class);

        return $unserializedObject;
    }
}
