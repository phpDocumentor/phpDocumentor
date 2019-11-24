<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Configuration;
use phpDocumentor\Transformer\Configuration\ExternalClassDocumentation;

class ExternalRouterTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfNoUrlIsGeneratedWhenThereIsNoDefinition() : void
    {
        $this->markTestSkipped('External router needs to be redefined, config is missing');
        // Arrange
        $router = new ExternalRouter();

        // Act
        $result = $router->match('My_Space_With_Suffix');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfSingleDefinitionGeneratesAnUrl() : void
    {
        $this->markTestSkipped('External router needs to be redefined, config is missing');
        // Arrange
//        $config->getTransformer()->setExternalClassDocumentation(
//            [new ExternalClassDocumentation('My_Space', 'http://abc/{CLASS}.html')]
//        );

        $router = new ExternalRouter();

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers \phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfMultipleDefinitionsGenerateAnUrl() : void
    {
        $this->markTestSkipped('External router needs to be redefined, config is missing');
        // Arrange
//        $config = new Configuration();
//        $config->getTransformer()->setExternalClassDocumentation(
//            [
//                new ExternalClassDocumentation('My_Zen_Space', 'http://abc/zen/{CLASS}.html'),
//                new ExternalClassDocumentation('My_Space', 'http://abc/{CLASS}.html'),
//            ]
//        );
        $router = new ExternalRouter();

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }
}
