<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console\Command\Helper;

use Mockery as m;
use phpDocumentor\Configuration;
use Symfony\Component\Console\Input\InputInterface;

/**
 * @coversDefaultClass \phpDocumentor\Application\Console\Command\Helper\ConfigurationHelper
 */
class ConfigurationHelperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var ConfigurationHelper */
    private $fixture;

    /** @var Configuration */
    private $configuration;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->configuration = new Configuration();
        $this->fixture = new ConfigurationHelper($this->configuration);
    }

    /**
     * @covers ::__construct
     */
    public function testIfDependenciesAreCorrectlyRegistered()
    {
        $this->assertAttributeSame($this->configuration, 'configuration', $this->fixture);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     */
    public function testIfSimpleValueFromInputIsReturned()
    {
        // Arrange
        $optionName = 'myOption';
        $expected = 'value';

        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, $expected);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers ::getOption
     * @covers ::splitCommaSeparatedValues
     * @covers ::valueIsEmpty
     */
    public function testIfSimpleCommaSeparatedValueFromInputIsReturnedAsArray()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, 'value1,value2');

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, null, true);

        // Assert
        $this->assertSame(['value1', 'value2'], $result);
    }

    /**
     * @covers ::getOption
     * @covers ::splitCommaSeparatedValues
     * @covers ::valueIsEmpty
     */
    public function testIfArrayContainingCommaSeparatedValueFromInputIsReturnedAsSimpleArray()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, ['value1,value2']);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, null, true);

        // Assert
        $this->assertSame(['value1', 'value2'], $result);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     */
    public function testIfEmptyArrayIsReturnedAsEmptyArrayEvenWhenDefaultIsNull()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, []);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, null);

        // Assert
        $this->assertSame([], $result);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     */
    public function testIfEmptyValueIsReturnedAsDefault()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, null);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, 'default');

        // Assert
        $this->assertSame('default', $result);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     * @covers ::getConfigValueFromPath
     */
    public function testIfValueIsRetrievedFromConfigIfNotInInput()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, null);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, 'parser/defaultPackageName');

        // Assert
        $this->assertSame($this->configuration->getParser()->getDefaultPackageName(), $result);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     * @covers ::getConfigValueFromPath
     */
    public function testIfValueIsNullWhenANonExistingConfigPathIsGiven()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, null);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, 'parser/defaultPackageName/notThere');

        // Assert
        $this->assertNull($result);
    }

    /**
     * @covers ::getOption
     * @covers ::valueIsEmpty
     * @covers ::getConfigValueFromPath
     */
    public function testIfValueIsNotRetrievedFromConfigIfItIsInInput()
    {
        // Arrange
        $optionName = 'myOption';
        $expected = 'option';
        $inputMock = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, $expected);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, 'parser/defaultPackageName');

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * Creates a mock of an input interface and returns it.
     *
     * @return m\MockInterface|InputInterface
     */
    private function givenAnInputObject()
    {
        return m::mock('Symfony\Component\Console\Input\InputInterface');
    }

    /**
     * Makes sure the getOption method of the input returns the expected value when a specific name is requested.
     *
     * @param m\MockInterface|InputInterface $inputMock
     * @param string                         $optionName
     * @param mixed                          $expected
     */
    private function whenAnOptionIsRetrievedFromInput($inputMock, $optionName, $expected)
    {
        $inputMock->shouldReceive('getOption')->with($optionName)->andReturn($expected);
    }
}
