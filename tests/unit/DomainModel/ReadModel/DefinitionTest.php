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

/**
 * @coversDefaultClass phpDocumentor\DomainModel\ReadModel\Definition
 * @covers ::<private>
 */
final class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getName
     * @covers ::getType
     * @covers ::getFilters
     * @covers ::getProperties
     */
    public function itRegistersTheNameTypeFiltersAndPropertiesToConstructAReadModel()
    {
        $name = 'name';
        $type = new Type('all');
        $filters = ['filter'];
        $properties = ['property'];

        $definition = new Definition($name, $type, $filters, $properties);

        $this->assertSame($name, $definition->getName());
        $this->assertSame($type, $definition->getType());
        $this->assertSame($filters, $definition->getFilters());
        $this->assertSame($properties, $definition->getProperties());
    }
}
