<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

use phpDocumentor\Dsn;

interface Upgradable
{
    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * Attempt to upgrade the given result of this definition to a newer version of the configuration.
     *
     * phpDocumentor attempts to auto-upgrade the configuration version to the latest version so that the internals of
     * phpDocumentor only need to care about the format of the latest version of the configuration and the rest will
     * be dealt with automatically.
     *
     * In order to achieve this, the result of this definition is passed into this upgrade function and it should output
     * the same settings in the structure that a newer definition expects (what that structure is depends on the version
     * of the definition.
     *
     * The 'configVersion' field in the result will inform the ConfigurationFactory what the next Configuration
     * definition should be used to parse this result.
     *
     * @param array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, versions?: array<string, array{api: array<int, array{ignore-tags: array, extensions: non-empty-array<string>, markers: non-empty-array<string>, visibillity: string, source: array{dsn: Dsn, paths: array}, ignore: array{paths: array}}>, apis: array, guides: array}>, settings?: array<mixed>, templates?: non-empty-list<string>, transformer: array{target: string}, parser: array{target: string, default-package-name: string, extensions: array{extensions: array}, visibility: string, markers: array{items: array}}, files: array{files: array, directories: array, ignores: array}, transformations: array{templates: array<string>}} $values
     *
     * @return array{configVersion: string, paths: array{cache: string, output: string}, templates: non-empty-list<string>, title: string, version: array{array{api: array{array{default-package-name: mixed, extensions: array{extensions: mixed}, ignore: array{paths: array<string>}, markers: array{markers: mixed}, source: array{paths: array<string>}, visibilities: non-empty-list<string>|null}}, number: string}}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function upgrade(array $values) : array;
}
