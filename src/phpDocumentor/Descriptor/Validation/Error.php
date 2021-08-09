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

namespace phpDocumentor\Descriptor\Validation;

/**
 * Struct to record a validation error.
 */
class Error
{
    /** @var string $severity */
    protected $severity;

    /** @var string $code */
    protected $code;

    /** @var int $line */
    protected $line = 0;

    /** @var mixed[] $context */
    protected $context = [];

    /**
     * @param mixed[] $context
     */
    public function __construct(string $severity, string $code, ?int $line, array $context = [])
    {
        $this->severity = $severity;
        $this->code     = $code;
        $this->line     = $line ?? 0;
        $this->context  = $context;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getSeverity(): string
    {
        return $this->severity;
    }

    /**
     * @return mixed[]
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
