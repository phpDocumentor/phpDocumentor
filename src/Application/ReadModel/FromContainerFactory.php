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

namespace phpDocumentor\Application\ReadModel;

use Interop\Container\ContainerInterface;
use phpDocumentor\DomainModel\ReadModel\Mapper;
use phpDocumentor\Application\ReadModel;
use phpDocumentor\DomainModel\ReadModel\Mapper\Factory;
use phpDocumentor\DomainModel\ReadModel\Type;

class FromContainerFactory implements Factory
{
    /** @var ContainerInterface */
    private $container;

    /** @var string[] */
    private $mapperAliases;

    public function __construct(ContainerInterface $container, array $mapperAliases = [])
    {
        $this->container     = $container;
        $this->mapperAliases = $mapperAliases;
    }

    /**
     * Returns a mapper for the given type of view.
     *
     * @param Type $viewType
     *
     * @return Mapper
     */
    public function create(Type $viewType)
    {
        $mapperName = (string)$viewType;
        if (isset($this->mapperAliases[$mapperName])) {
            $mapperName = $this->mapperAliases[$mapperName];
        }

        return $this->container->get($mapperName);
    }
}
