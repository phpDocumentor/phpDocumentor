<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Transformation;

/**
 * Base class for the actual transformation business logic (writers).
 */
abstract class WriterAbstract
{
    /**
     * This method verifies whether PHP has all requirements needed to run this writer.
     *
     * If one of the requirements is missing for this Writer then an exception of type RequirementMissing
     * should be thrown; this indicates to the calling process that this writer will not function.
     *
     * @throws Exception\RequirementMissing when a requirements is missing stating which one.
     */
    public function checkRequirements()
    {
        // empty body since most writers do not have requirements
    }

    /**
     * Checks if there is a space in the path.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException if path contains a space.
     */
    protected function checkForSpacesInPath($path)
    {
        if (strpos($path, ' ') !== false) {
            throw new \InvalidArgumentException('No spaces allowed in destination path: ' . $path);
        }
    }

    /**
     * Abstract definition of the transformation method.
     *
     * @param ProjectDescriptor $project        Document containing the structure.
     * @param Transformation    $transformation Transformation to execute.
     */
    abstract public function transform(ProjectDescriptor $project, Transformation $transformation);

    public function __toString(): string
    {
        return get_class($this);
    }
}
