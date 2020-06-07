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

namespace phpDocumentor\Descriptor;

/**
 * Represents the package for a class, trait, interface or file.
 *
 * @api
 * @package phpDocumentor\AST
 */
class PackageDescriptor extends NamespaceDescriptor implements Interfaces\PackageInterface
{
}
