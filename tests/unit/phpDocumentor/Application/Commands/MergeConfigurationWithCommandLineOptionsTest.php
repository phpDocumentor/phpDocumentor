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
use phpDocumentor\DomainModel\MergeConfigurationWithCommandLineOptions;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\MergeConfigurationWithCommandLineOptions
 * @covers ::__construct
 */
class MergeConfigurationWithCommandLineOptionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getArguments
     * @covers ::getOptions
     */
    public function testIfCommandIsProperlyCreatedAndReturnsParameters()
    {
        $options = ['options'];
        $arguments = ['arguments'];

        $fixture = new MergeConfigurationWithCommandLineOptions($options, $arguments);

        $this->assertSame($arguments, $fixture->getArguments());
        $this->assertSame($options, $fixture->getOptions());
    }
}
