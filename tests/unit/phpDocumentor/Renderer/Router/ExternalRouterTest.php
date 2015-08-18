<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Renderer\Router;

use phpDocumentor\Configuration;
use phpDocumentor\Renderer\Configuration\ExternalClassDocumentation;

class ExternalRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::match
     */
    public function testIfNoUrlIsGeneratedWhenThereIsNoDefinition()
    {
        $this->markTestIncomplete();
        // Arrange
        $config = new Configuration();
        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::match
     */
    public function testIfSingleDefinitionGeneratesAnUrl()
    {
        $this->markTestIncomplete();
        // Arrange
        $config = new Configuration();
        $config->getRenderer()->setExternalClassDocumentation(
            array(new ExternalClassDocumentation('My_Space', 'http://abc/{CLASS}.html'))
        );

        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }

    /**
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Renderer\Router\ExternalRouter::match
     */
    public function testIfMultipleDefinitionsGenerateAnUrl()
    {
        // Arrange
        $config = new Configuration();
        $config->getRenderer()->setExternalClassDocumentation(
            array(
                new ExternalClassDocumentation('My_Zen_Space', 'http://abc/zen/{CLASS}.html'),
                new ExternalClassDocumentation('My_Space', 'http://abc/{CLASS}.html')
            )
        );
        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }
}
