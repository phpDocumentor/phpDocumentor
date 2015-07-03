<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use Mockery as m;
use phpDocumentor\Configuration;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\InitializeParser
 */
class InitializeParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getConfiguration
     * @uses phpDocumentor\Configuration
     */
    public function testIfConfigurationIsRegistered()
    {
        $configuration = m::mock(Configuration::class);
        $fixture = new InitializeParser($configuration);

        $this->assertSame($configuration, $fixture->getConfiguration());
    }
}
