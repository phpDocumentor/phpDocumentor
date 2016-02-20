<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\ReadModel;

final class Definition
{
    /** @var string */
    private $name;

    /** @var Type */
    private $type;

    /** @var Filter[] */
    private $filters = [];

    /** @var string[] */
    private $properties = [];

    public function __construct($name, Type $type, array $filters = [], array $properties = [])
    {
        $this->name       = $name;
        $this->type       = $type;
        $this->filters    = $filters;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Filter[]
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @return string[]
     */
    public function getProperties()
    {
        return $this->properties;
    }
}
