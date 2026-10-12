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
use phpDocumentor\Guides\Compiler\CompilerContextInterface;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive;
use phpDocumentor\Guides\RestructuredText\Directives\BaseDirective;
use phpDocumentor\Guides\RestructuredText\Nodes\DirectiveNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveInfoNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveListNode;
use phpDocumentor\GuidesDirectives\Nodes\DirectiveOptionNode;
use phpDocumentor\Query\Engine;

use function array_map;
use function array_values;
use function count;
use function implode;
use function iterator_to_array;
use function preg_match_all;
use function strcmp;
use function strrchr;
use function strtolower;
use function substr;
use function trim;
use function usort;

use const PREG_SET_ORDER;

#[Directive(name: 'phpdoc-guides:directive-list')]
final class DirectiveList extends BaseDirective
{
    private const DIRECTIVE_ATTRIBUTE = '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\Directive';
    private const OPTION_ATTRIBUTE = '\\phpDocumentor\\Guides\\RestructuredText\\Directives\\Attributes\\Option';

    public function __construct(private Engine $engine)
    {
    }

    public function createNode(
        DirectiveNode $directiveNode,
        CompilerContextInterface|null $compilerContext = null,
    ): Node|null {
        if ($compilerContext instanceof DescriptorAwareCompilerContext === false) {
            return null;
        }

        $classes = iterator_to_array($this->engine->perform(
            $compilerContext->getVersionDescriptor(),
            //phpcs:ignore Generic.Files.LineLength.TooLong
            '$.documentationSets[*][?(type(@) == \'ApiSetDescriptor\')].indexes.classes[*][?(@.attributes[?(@.attribute == "\phpDocumentor\Guides\RestructuredText\Directives\Attributes\Directive")])]',
        ), false);

        $infos = [];
        foreach ($classes as $class) {
            if ($class instanceof ClassDescriptor === false || $class->isAbstract()) {
                continue;
            }

            $info = $this->createInfoNode($class);
            if ($info === null || isset($infos[$info->getName()])) {
                continue;
            }

            $infos[$info->getName()] = $info;
        }

        if (count($infos) === 0) {
            return null;
        }

        $infos = array_values($infos);
        usort($infos, static fn (DirectiveInfoNode $a, DirectiveInfoNode $b) => strcmp($a->getName(), $b->getName()));

        return new DirectiveListNode($infos);
    }

    private function createInfoNode(ClassDescriptor $class): DirectiveInfoNode|null
    {
        $directive = null;
        $options = [];
        foreach ($class->getAttributes() as $attribute) {
            $fqsen = (string) $attribute->getFullyQualifiedStructuralElementName();
            if ($fqsen === self::DIRECTIVE_ATTRIBUTE) {
                $directive = $attribute;
            } elseif ($fqsen === self::OPTION_ATTRIBUTE) {
                $options[] = $this->createOptionNode($attribute);
            }
        }

        $name = $directive?->getArgument('name');
        if ($directive === null || $name === null) {
            return null;
        }

        $aliases = $directive->getArgument('aliases');

        return new DirectiveInfoNode(
            $this->unquote($name->getValue()),
            $aliases === null ? [] : $this->parseStringList($aliases->getValue()),
            $this->createDescription($class),
            $options,
        );
    }

    private function createOptionNode(AttributeInterface $attribute): DirectiveOptionNode
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

    private function createDescription(ClassDescriptor $class): string
    {
        $parts = [];
        if ($class->getSummary() !== '') {
            $parts[] = $class->getSummary();
        }

        $body = (string) $class->getDescription();
        if ($body !== '') {
            $parts[] = $body;
        }

        return implode("\n\n", $parts);
    }

    private function unquote(string $value): string
    {
        return trim($value, '\'"');
    }

    private function enumCaseName(string $value): string
    {
        $case = strrchr($value, ':');

        return strtolower($case === false ? $this->unquote($value) : substr($case, 1));
    }

    /** @return string[] */
    private function parseStringList(string $value): array
    {
        preg_match_all('/\'([^\']*)\'|"([^"]*)"/', $value, $matches, PREG_SET_ORDER);

        return array_values(array_map(
            static fn (array $match): string => $match[2] ?? $match[1],
            $matches,
        ));
    }
}
