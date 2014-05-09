<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use Zend\Config\Config;

class ExternalRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfNoUrlIsGeneratedWhenThereIsNoDefinition()
    {
        // Arrange
        $config = new Config(array('transformer' => array()));
        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfSingleDefinitionGeneratesAnUrl()
    {
        // Arrange
        $config = new Config(
            array(
                'transformer' => array(
                    'external-class-documentation' => array('prefix' => 'My_Space', 'uri' => 'http://abc/{CLASS}.html')
                )
            )
        );
        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::__construct
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::configure
     * @covers phpDocumentor\Transformer\Router\ExternalRouter::match
     */
    public function testIfMultipleDefinitionsGenerateAnUrl()
    {
        // Arrange
        $config = new Config(
            array(
                'transformer' => array(
                    'external-class-documentation' => array(
                        array('prefix' => 'My_Zen_Space', 'uri' => 'http://abc/zen/{CLASS}.html'),
                        array('prefix' => 'My_Space', 'uri' => 'http://abc/{CLASS}.html')
                    )
                )
            )
        );
        $router = new ExternalRouter($config);

        // Act
        $result = $router->match('My_Space_With_Suffix')->generate('My_Space_With_Suffix');

        // Assert
        $this->assertSame('http://abc/My_Space_With_Suffix.html', $result);
    }
}
