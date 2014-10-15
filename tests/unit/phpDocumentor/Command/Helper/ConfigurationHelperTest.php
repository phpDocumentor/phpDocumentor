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

namespace phpDocumentor\Command\Helper;

use Mockery as m;
use phpDocumentor\Configuration;
use Symfony\Component\Console\Input\InputInterface;

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
        $this->fixture = new ConfigurationHelper($this->configuration);
    }

    /**
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::__construct
     */
    public function testIfDependenciesAreCorrectlyRegistered()
    {
        $this->assertAttributeSame($this->configuration, 'configuration', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
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
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::splitCommaSeparatedValues
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
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
        $this->assertSame(array('value1', 'value2'), $result);
    }

    /**
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::splitCommaSeparatedValues
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
     */
    public function testIfArrayContainingCommaSeparatedValueFromInputIsReturnedAsSimpleArray()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock  = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, array('value1,value2'));

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, null, true);

        // Assert
        $this->assertSame(array('value1', 'value2'), $result);
    }

    /**
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
     */
    public function testIfEmptyArrayIsReturnedAsEmptyArrayEvenWhenDefaultIsNull()
    {
        // Arrange
        $optionName = 'myOption';
        $inputMock  = $this->givenAnInputObject();
        $this->whenAnOptionIsRetrievedFromInput($inputMock, $optionName, array());

        // Act
        $result = $this->fixture->getOption($inputMock, $optionName, null, null);

        // Assert
        $this->assertSame(array(), $result);
    }

    /**
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
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
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getConfigValueFromPath
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
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getConfigValueFromPath
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
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getOption
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::valueIsEmpty
     * @covers phpDocumentor\Command\Helper\ConfigurationHelper::getConfigValueFromPath
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
        return m::mock('Symfony\Component\Console\Input\InputInterface');
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
