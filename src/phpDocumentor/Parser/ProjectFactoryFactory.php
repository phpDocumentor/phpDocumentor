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

namespace phpDocumentor\Parser;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Php\Factory;
use phpDocumentor\Reflection\Php\NodesFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Reflection\PrettyPrinter;

final class ProjectFactoryFactory
{
    public static function create(iterable $fileMiddlewaresBuilder): ProjectFactory
    {
        $fileMiddlewares = [];
        foreach ($fileMiddlewaresBuilder as $middleware) {
            $fileMiddlewares[] = $middleware;
        }

        $strategies = [
            new Factory\Argument(new PrettyPrinter()),
            new Factory\Class_(),
            new Factory\Constant(new PrettyPrinter()),
            new Factory\DocBlock(DocBlockFactory::createInstance()),
            new Factory\Function_(),
            new Factory\Interface_(),
            new Factory\Method(),
            new Factory\Property(new PrettyPrinter()),
            new Factory\Trait_(),
            new Factory\File(
                NodesFactory::createInstance(),
                $fileMiddlewares
            ),
        ];

        return new ProjectFactory($strategies);
    }
}
