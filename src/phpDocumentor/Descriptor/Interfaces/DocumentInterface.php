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

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Guides\Nodes\DocumentNode;

interface DocumentInterface extends Descriptor
{
    public function getDocumentNode(): DocumentNode;

    public function setDocumentNode(DocumentNode $documentNode): void;

    public function getHash(): string;

    public function getFile(): string;

    public function getTitle(): string;
}
