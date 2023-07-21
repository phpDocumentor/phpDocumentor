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

namespace phpDocumentor\Configuration;

class VersionSpecification
{
    /**
     * @param array<int, ApiSpecification> $api
     * @param array<mixed>|null $guides
     */
    public function __construct(private readonly string $number, public array $api, public array|null $guides)
    {
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /** @return array<int, ApiSpecification> */
    public function getApi(): array
    {
        return $this->api;
    }

    /** @param array<int, ApiSpecification> $api */
    public function setApi(array $api): void
    {
        $this->api = $api;
    }

    public function addApi(ApiSpecification $api): void
    {
        $this->api[] = $api;
    }

    /** @return array<mixed>|null */
    public function getGuides(): array|null
    {
        return $this->guides;
    }
}
