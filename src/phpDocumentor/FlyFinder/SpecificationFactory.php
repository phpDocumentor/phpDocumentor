<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\FlyFinder;


use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;
use Flyfinder\Specification\SpecificationInterface;

final class SpecificationFactory
{
    /**
     * @param array $ignore
     * @param array $extensions
     *
     * @return SpecificationInterface
     */
    public function create(array $ignore, array $extensions)
    {
        $ignoreSpec = null;
        if (isset($ignore['paths'])) {
            foreach ($ignore['paths'] as $path) {
                $ignoreSpec = $this->orSpec($this->inPath($path), $ignoreSpec);
            }
        }

        if (isset($ignore['hidden']) && $ignore['hidden'] === true) {
            $ignoreSpec = $this->orSpec(new IsHidden(), $ignoreSpec);
        }

        return new AndSpecification(new NotSpecification($ignoreSpec), new HasExtension($extensions));
    }

    /**
     * Will return an OrSpecification when $or and $spec are both not null.
     *
     * @param SpecificationInterface $or
     * @param SpecificationInterface|null $spec
     * @return OrSpecification|SpecificationInterface
     */
    private function orSpec(SpecificationInterface $or, SpecificationInterface $spec = null)
    {
        if ($spec === null) {
            return $or;
        }

        return new OrSpecification($spec, $or);
    }

    private function inPath($path)
    {
        return new InPath(new Path($path));
    }
}

