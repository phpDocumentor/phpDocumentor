<?php
/**
 * DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated Test
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
/**
 * Test class for DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
class DocBlox_Plugin_Core_Parser_DocBlock_Validator_DeprecatedTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that isValid can interpret the configuration options and log parse
     * errors as required
     *
     * @param array  $options     Options from the plugin.xml file
     * @param string $entity      The entity we are validating
     * @param int    $lineNumber  The line number of the entity
     * @param int    $tagCount    The amount of tags found
     * @param string $logCount    PHPUnit expects assertion string
     * @param string $expectedLog The line we expect to be logged
     *
     * @covers DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated::isValid
     * @covers DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated::validateTags
     * @dataProvider provideDataForIsValid
     *
     * @return void
     */
    public function testIsValidCanLogErrorsDependingOnConfigurationOptions($docblock, $options, $entity, $lineNumber, $tagCount, $logCount, $expectedLog)
    {
        $docblock = new DocBlox_Reflection_DocBlock(
            $docblock
        );

        $val = $this->getMock(
            'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated',
            array('logParserError', 'debug'),
            array($entity, $lineNumber, $docblock)
        );

        if ($logCount != 'never') {
            $val->expects($this->$logCount())
                ->method('logParserError')
                ->with($this->equalTo('CRITICAL'), $this->equalTo($expectedLog), $this->equalTo($lineNumber));
        } else {
            $val->expects($this->$logCount())
                ->method('logParserError');
        }

        $val->setOptions($options);

        $val->isValid();
    }

    /**
     * Data provider for testIsValidLogsErrorsForMissingRequiredTags
     *
     * @return array
     */
    public function provideDataForIsValid()
    {
        return array(
            // Data Set 0
            // Found deprecated tag, therefore logged
            array(
                '/**
                  * Short description
                  *
                  * @deprecated
                  */',
                array(
                    'deprecated' => array(
                        '__ALL__' => array('deprecated')
                    )
                ),
                'File.php',
                1,
                0,
                'once',
                'Found deprecated tag "deprecated" in File.php'
            ),

            // Data Set 1
            // Didn't find deprecated tag, therefore not logged
            array(
                '/**
                  * Short description
                  *
                  */',
                array(
                    'deprecated' => array(
                        '__ALL__' => array('deprecated')
                    )
                ),
                'File.php',
                1,
                0,
                'never',
                ''
            ),

            // Data Set 2
            // Found deprecated tag, therefore logged
            array(
                '/**
                  * Short description
                  *
                  * @deprecated
                  */',
                array(
                    'deprecated' => array(
                        'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated' => array('deprecated')
                    )
                ),
                'File.php',
                1,
                0,
                'once',
                'Found deprecated tag "deprecated" in File.php'
            ),

            // Data Set 3
            // Didn't find deprecated tag, therefore not logged
            array(
                '/**
                  * Short description
                  *
                  */',
                array(
                    'deprecated' => array(
                        'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Deprecated' => array('deprecated')
                    )
                ),
                'File.php',
                1,
                0,
                'never',
                ''
            ),
        );
    }
}