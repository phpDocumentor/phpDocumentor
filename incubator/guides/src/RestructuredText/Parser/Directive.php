<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser;

class Directive
{
    /** @var string */
    private $variable;

    /** @var string */
    private $name;

    /** @var string */
    private $data;

    /** @var mixed[] */
    private $options;

    /**
     * @param mixed[] $options
     */
    public function __construct(string $variable, string $name, string $data, array $options = [])
    {
        $this->variable = $variable;
        $this->name = $name;
        $this->data = $data;
        $this->options = $options;
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @return mixed[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed $value
     */
    public function setOption(string $key, $value): void
    {
        $this->options[$key] = $value;
    }
}
