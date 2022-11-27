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

namespace phpDocumentor\Descriptor\ValueObjects;

/**
 * @implements Equals<IsApplicable>
 * @immutable
 */
final class IsApplicable implements Equals
{
    private bool $value;

    private function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function true(): self
    {
        return new self(true);
    }

    public static function false(): self
    {
        return new self(false);
    }

    public static function fromBoolean(bool $boolean): self
    {
        return new self($boolean);
    }

    public function isTrue(): bool
    {
        return $this->value === true;
    }

    public function isFalse(): bool
    {
        return $this->value === false;
    }

    public function inverse(): self
    {
        return new self(!$this->value);
    }

    public function equals(Equals $other): bool
    {
        return $other instanceof self
            && $this->value === $other->value;
    }
}
