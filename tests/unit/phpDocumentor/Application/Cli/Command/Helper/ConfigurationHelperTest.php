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

namespace phpDocumentor\Application\Cli\Command\Helper;

use Mockery as m;
use phpDocumentor\Configuration;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Tests for the phpDocumentor ConfigurationHelper class.
 *
 * @coversDefaultClass phpDocumentor\Application\Cli\Command\Helper\ConfigurationHelper
 */
class ConfigurationHelperTest extends \PHPUnit_Framework_TestCase
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
        $this->fixture       = new ConfigurationHelper($this->configuration);
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
        $expected   = 'value';

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
        $inputMock  = $this->givenAnInputObject();
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
        $inputMock  = $this->givenAnInputObject();
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
        $inputMock  = $this->givenAnInputObject();
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
        $inputMock  = $this->givenAnInputObject();
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
        $inputMock  = $this->givenAnInputObject();
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
        $inputMock  = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, null);

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, 'parser/defaultPackageName/notThere');

        // Assert
        $this->assertSame(null, $result);
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
        $expected   = 'option';
        $inputMock  = $this->givenAnInputObject();
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
        return m::mock(InputInterface::class);
    }

    /**
     * Makes sure the getOption method of the input returns the expected value when a specific name is requested.
     *
     * @param m\MockInterface|InputInterface $inputMock
     * @param string                         $optionName
     * @param mixed                          $expected
     *
     * @return void
     */
    private function whenAnOptionIsRetrievedFromInput($inputMock, $optionName, $expected)
    {
        $inputMock->shouldReceive('getOption')->with($optionName)->andReturn($expected);
    }
}
