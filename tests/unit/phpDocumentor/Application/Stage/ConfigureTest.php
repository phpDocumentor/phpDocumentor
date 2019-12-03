<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
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
    public function testInvokeOverridesConfig() : void
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
