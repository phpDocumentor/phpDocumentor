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

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

class Payload
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ProjectDescriptorBuilder
     */
    private $builder;

    public function __construct(array $config, ProjectDescriptorBuilder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    public function getConfig() : array
    {
        return $this->config;
    }

    public function getBuilder() : ProjectDescriptorBuilder
    {
        return $this->builder;
    }
}
