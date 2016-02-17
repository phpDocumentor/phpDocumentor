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

final class Factory implements Mapper
{
    /** @var Factory */
    private $mapperFactory;

    public function __construct(Mapper\Factory $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(Definition $readModelDefinition, $documentation)
    {
        $mapper = $this->mapperFactory->create($readModelDefinition->getType());
        $data = $mapper->create($readModelDefinition, $documentation);

        foreach ($readModelDefinition->getFilters() as $filter) {
            $data = $filter($data);
        }

        return new ReadModel($readModelDefinition, $data);
    }
}
