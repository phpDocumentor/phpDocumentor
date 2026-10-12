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

namespace phpDocumentor\GuidesDirectives\Directives;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionNode;

use function strrchr;
use function strtolower;
use function substr;
use function trim;

final class DirectiveOptionNodeFactory
{
    public const DIRECTIVE_ATTRIBUTE = '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\Directive';
    public const OPTION_ATTRIBUTE = '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\Option';

    /** @return DirectiveOptionNode[] */
    public function createFromClass(ClassDescriptor $class): array
    {
        $options = [];
        foreach ($class->getAttributes() as $attribute) {
            if ((string) $attribute->getFullyQualifiedStructuralElementName() !== self::OPTION_ATTRIBUTE) {
                continue;
            }

            $options[] = $this->create($attribute);
        }

        return $options;
    }

    public function unquote(string $value): string
    {
        return trim($value, '\'"');
    }

    private function create(AttributeInterface $attribute): DirectiveOptionNode
    {
        $type = $attribute->getArgument('type');
        $default = $attribute->getArgument('default');
        $description = $attribute->getArgument('description');

        return new DirectiveOptionNode(
            $this->unquote($attribute->getArgument('name')?->getValue() ?? ''),
            $type === null ? 'string' : $this->enumCaseName($type->getValue()),
            $default === null ? null : $this->unquote($default->getValue()),
            $description === null ? '' : $this->unquote($description->getValue()),
        );
    }

    private function enumCaseName(string $value): string
    {
        $case = strrchr($value, ':');

        return strtolower($case === false ? $this->unquote($value) : substr($case, 1));
    }
}
