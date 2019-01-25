<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Configuration\ConfigurationFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @coversDefaultClass \phpDocumentor\Application\Stage\Configure
 */
class ConfigureTest extends TestCase
{
    /**
     * @use \phpDocumentor\Configuration\ConfigurationFactory;
     * @use \phpDocumentor\Configuration\Factory\Version3;
     * @use \phpDocumentor\DomainModel\Uri;
     */
    public function testInvokeOverridesConfig()
    {
        $configFactory = new ConfigurationFactory([], []);

        $fixture = new Configure(
            $configFactory,
            $configFactory->fromDefaultLocations(),
            new NullLogger()
        );

        $result = $fixture(['force' => true]);

        $this->assertFalse($result['phpdocumentor']['use-cache']);
    }
}
