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

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Formats\OutputFormat;
use phpDocumentor\Guides\Nodes\DocumentNode;

interface MarkupLanguageParser
{
    public function supports(string $inputFormat, OutputFormat $outputFormat): bool;

    public function getEnvironment(): Environment;

    public function getReferenceBuilder(): ReferenceBuilder;

    public function parse(Environment $environment, string $contents): DocumentNode;

    public function getDocument(): DocumentNode;
}
