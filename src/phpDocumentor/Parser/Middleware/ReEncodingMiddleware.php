<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Parser\ReEncodedFile;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use Symfony\Component\String\ByteString;
use Webmozart\Assert\Assert;

final class ReEncodingMiddleware implements Middleware
{
    /** @var string */
    private $encoding = 'UTF-8';

    public function withEncoding(string $encoding) : void
    {
        $this->encoding = $encoding;
    }

    /**
     * @param callable(Command): object $next
     */
    public function execute(Command $command, callable $next) : object
    {
        Assert::isInstanceOf($command, CreateCommand::class);

        $file = new ReEncodedFile(
            $command->getFile()->path(),
            (new ByteString($command->getFile()->getContents()))->toUnicodeString($this->encoding)
        );

        return $next(new CreateCommand($file, $command->getStrategies()));
    }
}
