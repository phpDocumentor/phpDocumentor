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

final class ReadModel
{
    /** @var Definition */
    private $definition;

    /** @var mixed */
    private $data;

    public function __construct(Definition $definition, $data)
    {
        $this->definition = $definition;
        $this->data       = $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->definition->getName();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->data;
    }
}
