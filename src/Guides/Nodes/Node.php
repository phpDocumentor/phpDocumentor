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

namespace phpDocumentor\Guides\Nodes;

use function implode;
use function is_callable;
use function is_string;
use function strlen;
use function substr;
use function trim;

abstract class Node
{
    /** @var Node|callable|string|null */
    protected $value;

    /** @var string[] */
    protected $classes = [];

    /** @var mixed[] */
    private $options;

    /**
     * @param Node|callable|string|null $value
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }

    /**
     * @return Node|callable|string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Node|callable|string|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string[]
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    public function getClassesString(): string
    {
        return implode(' ', $this->classes);
    }

    /**
     * @param string[] $classes
     */
    public function setClasses(array $classes): void
    {
        $this->classes = $classes;
    }

    public function getValueString(): string
    {
        if ($this->value === null) {
            return '';
        }

        if ($this->value instanceof self) {
            return $this->value->getValueString();
        }

        if (is_string($this->value)) {
            return $this->value;
        }

        if (is_callable($this->value)) {
            return ($this->value)();
        }

        return '';
    }

    /**
     * @param array<string, mixed> $options
     */
    public function withOptions(array $options): self
    {
        $result = clone $this;
        $result->options = $options;

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed|null $default
     *
     * @return mixed|null
     */
    public function getOption(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * @param string[] $lines
     */
    protected function normalizeLines(array $lines): string
    {
        if ($lines !== []) {
            $firstLine = $lines[0];

            $length = strlen($firstLine);
            for ($k = 0; $k < $length; $k++) {
                if (trim($firstLine[$k]) !== '') {
                    break;
                }
            }

            foreach ($lines as &$line) {
                $line = substr($line, $k);
            }
        }

        return implode("\n", $lines);
    }
}
