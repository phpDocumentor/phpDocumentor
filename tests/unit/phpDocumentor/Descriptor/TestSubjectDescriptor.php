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

final class TestSubjectDescriptor extends DescriptorAbstract
{
    public function setParent(TestSubjectDescriptor $descriptor)
    {
        $this->inheritedElement = $descriptor;
    }
}
