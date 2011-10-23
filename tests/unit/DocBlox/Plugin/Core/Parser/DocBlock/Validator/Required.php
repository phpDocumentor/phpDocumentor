<?php
/**
 * DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required Test
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
/**
 * Test class for DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
class DocBlox_Plugin_Core_Parser_DocBlock_Validator_RequiredTest extends PHPUnit_Framework_TestCase
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
     * @covers DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required::isValid
     * @covers DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required::validateTags
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
            'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required',
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
            // Cannot find tag that is required, therefore logged
            array(
                '/**
                  * Short description without access tag
                  *
                  *
                  */',
                array(
                    'required' => array(
                        '__ALL__' => array('access')
                    )
                ),
                'File.php',
                1,
                0,
                'once',
                'Not found required tag "access" in File.php'
            ),

            // Data Set 1
            // Find tag that is required, therefore not logged
            array(
                '/**
                  * Short description with access tag
                  *
                  * @access
                  */',
                array(
                    'required' => array(
                        '__ALL__' => array('access')
                    )
                ),
                'File.php',
                1,
                0,
                'never',
                ''
            ),

            // Data Set 2
            // Find tag that is required for certain type, therefore not logged
            array(
                '/**
                  * Short description with access tag
                  *
                  * @access
                  */',
                array(
                    'required' => array(
                        'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required' => array('access')
                    )
                ),
                'File.php',
                1,
                0,
                'never',
                ''
            ),

            // Data Set 3
            // Cannot find tag that is required for certain type, therefore logged
            array(
                '/**
                  * Short description without access tag
                  *
                  */',
                array(
                    'required' => array(
                        'DocBlox_Plugin_Core_Parser_DocBlock_Validator_Required' => array('access')
                    )
                ),
                'File.php',
                1,
                0,
                'once',
                'Not found required tag "access" in File.php'
            ),
        );
    }
}