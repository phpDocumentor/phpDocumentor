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

namespace phpDocumentor\Application;

use phpDocumentor\DomainModel\Path;

/**
 * @coversDefaultClass phpDocumentor\Application\ConfigureCache
 * @covers ::<private>
 */
final class ConfigureCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::location
     * @covers ::enabled
     */
    public function itShouldRegisterWhetherTheCacheIsEnabledAndTheCacheLocation()
    {
        $command = new ConfigureCache(new Path('\tmp'), true);
        $this->assertEquals(new Path('\tmp'), $command->location());
        $this->assertTrue($command->enabled());

        $command = new ConfigureCache(new Path('\tmp'), false);
        $this->assertFalse($command->enabled());
    }
}
