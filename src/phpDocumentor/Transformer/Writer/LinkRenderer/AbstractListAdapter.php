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

namespace phpDocumentor\Transformer\Writer\LinkRenderer;

use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use Webmozart\Assert\Assert;

use function implode;
use function is_array;
use function sprintf;

final class AbstractListAdapter implements LinkRendererInterface
{
    private LinkRendererInterface $rendererChain;

    public function __construct(LinkRendererInterface $rendererChain)
    {
        $this->rendererChain = $rendererChain;
    }

    /**
     * {@inheritDoc}
     */
    public function supports($value): bool
    {
        return $value instanceof AbstractList;
    }

    /**
     * {@inheritDoc}
     */
    public function render($value, string $presentation): string
    {
        Assert::isInstanceOf($value, AbstractList::class);

        $listType = $this->renderListType($value, $presentation);
        $keyType = $this->renderKeyType($value, $presentation);
        $valueType = $this->renderValueType($value, $presentation);

        return sprintf('%s&lt;%s, %s&gt;', $listType, $keyType, $valueType);
    }

    private function renderListType(AbstractList $node, string $presentation): string
    {
        $listType = 'mixed';
        if ($node instanceof Collection) {
            $listType = $this->rendererChain->render($node->getFqsen(), $presentation);
        }

        if ($node instanceof Array_) {
            $listType = 'array';
        }

        if ($node instanceof Iterable_) {
            $listType = 'iterable';
        }

        if (is_array($listType)) {
            $listType = implode('|', $listType);
        }

        return $listType;
    }

    private function renderKeyType(AbstractList $node, string $presentation): string
    {
        $keyType = $this->rendererChain->render($node->getKeyType(), $presentation);

        if (is_array($keyType)) {
            $keyType = implode('|', $keyType);
        }

        return $keyType;
    }

    private function renderValueType(AbstractList $node, string $presentation): string
    {
        $valueType = $this->rendererChain->render($node->getValueType(), $presentation);

        if (is_array($valueType)) {
            $valueType = implode('|', $valueType);
        }

        return $valueType;
    }
}
