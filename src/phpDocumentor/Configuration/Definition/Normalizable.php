<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Dsn;
use phpDocumentor\Path;

interface Normalizable
{
    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, mixed>, settings?: array<mixed>, templates?: non-empty-list<string>} $configuration
     *
     * @return array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, array{api: array<int, array{ignore-tags: array, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibillity: string, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}}>, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function normalize(array $configuration): array;
}
