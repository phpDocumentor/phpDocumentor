<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Ben Selby <benmatselby@gmail.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Validator;

/**
 * Test class for
 *    phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator
 */
class RequiredValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that isValid can interpret the configuration options and log parse
     * errors as required
     *
     * @param string $docblock    DocBlock contents.
     * @param array  $options     Options from the plugin.xml file
     * @param string $entity      The entity we are validating
     * @param int    $lineNumber  The line number of the entity
     * @param int    $tagCount    The amount of tags found
     * @param string $logCount    PHPUnit expects assertion string
     * @param string $expectedLog The line we expect to be logged
     *
     * @covers phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator::isValid
     * @covers phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator::validateTags
     *
     * @dataProvider provideDataForIsValid
     *
     * @return void
     */
    public function testIsValidCanLogErrorsDependingOnConfigurationOptions(
        $docblock, $options, $entity, $lineNumber, $tagCount, $logCount,
        $expectedLog
    ) {
        $docblock = new \phpDocumentor\Reflection\DocBlock(
            $docblock
        );

        $val = $this->getMock(
            '\phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator',
            array('logParserError', 'debug'),
            array($entity, $lineNumber, $docblock)
        );

        if ($logCount != 'never') {
            $val->expects($this->$logCount())
                ->method('logParserError')
                ->with(
                    $this->equalTo('CRITICAL'), $this->equalTo($expectedLog),
                    $this->equalTo($lineNumber)
                );
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
                        '\phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator' => array('access')
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
                        '\phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\RequiredValidator' => array('access')
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