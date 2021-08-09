<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration\Definition;

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
     * @param array<string, mixed> $values
     *
     * @return array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: string, cache: string}, version?: array{array{api: array{array{default-package-name: string, extensions: array{extensions: array<array-key, string>}, ignore: array{paths: array<array-key, string>}, markers: array{markers: array<array-key, mixed>}, source: array{paths: array<array-key, string>}, visibilities: non-empty-list<string>}}, number: string}}}, settings?: array<mixed>, templates?: non-empty-list<string>}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function upgrade(array $values): array;
}
