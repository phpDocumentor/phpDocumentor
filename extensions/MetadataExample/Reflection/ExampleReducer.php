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

namespace phpDocumentor\MetadataExample\Reflection;

use Override;
use phpDocumentor\Reflection\Metadata\MetaDataContainer;
use phpDocumentor\Reflection\Php\Factory\ContextStack;
use phpDocumentor\Reflection\Php\Factory\Reducer\Reducer;
use phpDocumentor\Reflection\Php\StrategyContainer;

use function method_exists;

final class ExampleReducer implements Reducer
{
    #[Override]
    public function reduce(
        ContextStack $context,
        object $object,
        StrategyContainer $strategies,
        object|null $carry,
    ): object|null {
        if ($carry instanceof MetaDataContainer && method_exists($carry, 'getName')) {
            $carry->addMetadata(new ExampleMetadata('metadata-for-' . $carry->getName()));
            $carry->addMetadata(new SecondMetadata('second-for-' . $carry->getName()));
            $carry->addMetadata(new UndeclaredMetadata('undeclared-for-' . $carry->getName()));
            $carry->addMetadata(new NoTemplateMetadata('notemplate-for-' . $carry->getName()));
        }

        return $carry;
    }
}
