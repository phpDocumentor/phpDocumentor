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

use phpDocumentor\Dsn;
use phpDocumentor\Path;

class VersionSpecification
{
    /** @var string */
    private $number;

    /**
     * @var array<
     *     int,
     *     array{
     *         source: array{dsn: Dsn, paths: array<Path>},
     *         output?: string,
     *         ignore?: array{paths: non-empty-array<Path>},
     *         extensions?: non-empty-list<string>,
     *         visibility?: array<string>,
     *         default-package-name?: string,
     *         include-source?: bool,
     *         markers?: non-empty-list<string>,
     *         ignore-tags?: non-empty-list<string>,
     *         examples?: array{dsn: Dsn, paths: list<string>},
     *         encoding?: string,
     *         validate?: bool
     *     }>
     */
    public $api;

    /** @var array<mixed>|null */
    public $guides;

    //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName squizlabs/PHP_CodeSniffer/issues/2591
    /**
     * @param array<
     *     int,
     *     array{
     *         source: array{dsn: Dsn, paths: array<Path>},
     *         output?: string,
     *         ignore?: array{paths: non-empty-array<Path>},
     *         extensions?: non-empty-list<string>,
     *         visibility?: array<string>,
     *         default-package-name?: string,
     *         include-source?: bool,
     *         markers?: non-empty-list<string>,
     *         ignore-tags?: non-empty-list<string>,
     *         examples?: array{dsn: Dsn, paths: list<string>}
     *     }> $api
     * @param array<mixed>|null $guides
     */
    //phpcs:enable Squiz.Commenting.FunctionComment.MissingParamName
    public function __construct(string $number, array $api, ?array $guides)
    {
        $this->number = $number;
        $this->api = $api;
        $this->guides = $guides;
    }

    public function getNumber() : string
    {
        return $this->number;
    }

    /**
     * @return array<
     *     int,
     *     array{
     *         source: array{dsn: Dsn, paths: array<Path>},
     *         output?: string,
     *         ignore?: array{paths: non-empty-array<Path>},
     *         extensions?: non-empty-list<string>,
     *         visibility?: array<string>,
     *         default-package-name?: string,
     *         include-source?: bool,
     *         markers?: non-empty-list<string>,
     *         ignore-tags?: non-empty-list<string>,
     *         examples?: array{dsn: Dsn, paths: list<string>}
     *     }>
     */
    public function getApi() : array
    {
        return $this->api;
    }

    //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName squizlabs/PHP_CodeSniffer/issues/2591
    /**
     * @param array<
     *     int,
     *     array{
     *         source: array{dsn: Dsn, paths: array<Path>},
     *         output?: string,
     *         ignore?: array{paths: non-empty-array<Path>},
     *         extensions?: non-empty-list<string>,
     *         visibility?: array<string>,
     *         default-package-name?: string,
     *         include-source?: bool,
     *         markers?: non-empty-list<string>,
     *         ignore-tags?: non-empty-list<string>,
     *         examples?: array{dsn: Dsn, paths: list<string>}
     *     }> $api
     */
    //phpcs:enable Squiz.Commenting.FunctionComment.MissingParamName
    public function setApi(array $api) : void
    {
        $this->api = $api;
    }

    //phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName squizlabs/PHP_CodeSniffer/issues/2591
    /**
     * @param array{
     *         source: array{dsn: Dsn, paths: array<Path>},
     *         output?: string,
     *         ignore?: array{paths: non-empty-array<Path>},
     *         extensions?: non-empty-list<string>,
     *         visibility?: array<string>,
     *         default-package-name?: string,
     *         include-source?: bool,
     *         markers?: non-empty-list<string>,
     *         ignore-tags?: non-empty-list<string>,
     *         examples?: array{dsn: Dsn, paths: list<string>}
     *     } $api
     */
    //phpcs:enable Squiz.Commenting.FunctionComment.MissingParamName
    public function addApi(array $api) : void
    {
        $this->api[] = $api;
    }

    /**
     * @return array<mixed>|null
     */
    public function getGuides() : ?array
    {
        return $this->guides;
    }
}
