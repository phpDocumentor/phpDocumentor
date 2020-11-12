<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Dsn;
use phpDocumentor\Path;

interface Normalizable
{
    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, mixed>, settings?: array<mixed>, templates?: non-empty-list<string>} $configuration
     *
     * @return array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, VersionSpecification>, settings?: array<mixed>, templates?: non-empty-list<string>}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function normalize(array $configuration) : array;
}
