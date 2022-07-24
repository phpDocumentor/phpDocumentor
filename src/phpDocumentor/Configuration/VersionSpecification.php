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
    private string $number;

    /** @var array<array-key, ApiSpecification> */
    public array $api = [];

    /** @var array<mixed>|null */
    public ?array $guides = null;

    /**
     * @param array<int, ApiSpecification> $api
     * @param array<mixed>|null $guides
     */
    public function __construct(string $number, array $api, ?array $guides)
    {
        $this->number = $number;
        $this->api = $api;
        $this->guides = $guides;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return array<int, ApiSpecification>
     */
    public function getApi(): array
    {
        return $this->api;
    }

    /**
     * @param array<int, ApiSpecification> $api
     */
    public function setApi(array $api): void
    {
        $this->api = $api;
    }

    public function addApi(ApiSpecification $api): void
    {
        $this->api[] = $api;
    }

    /**
     * @return array<mixed>|null
     */
    public function getGuides(): ?array
    {
        return $this->guides;
    }
}
