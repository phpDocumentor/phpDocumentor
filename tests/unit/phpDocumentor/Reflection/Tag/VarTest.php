<?php
/**
 * phpDocumentor Var Tag Test
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Daniel O'Connor <daniel.oconnor@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Test class for phpDocumentor_Reflection_DocBlock_Tag_Link
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Daniel O'Connor <daniel.oconnor@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
class phpDocumentor_Reflection_DocBlock_Tag_VarTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that the phpDocumentor_Reflection_DocBlock_Tag_Var can understand
     * the @var doc block
     *
     * @param string $type
     * @param string $content
     * @param string $exType
     * @param string $exVariable
     * @param string $exDescription
     *
     * @covers phpDocumentor_Reflection_DocBlock_Tag_Var::__construct
     * @dataProvider provideDataForConstuctor
     *
     * @return void
     */
    public function testConstructorParesInputsIntoCorrectFields(
        $type, $content, $exType, $exVariable, $exDescription
    )
    {
        $tag = new phpDocumentor_Reflection_DocBlock_Tag_Var($type, $content);

        $this->assertEquals($exType, $tag->getType());
        $this->assertEquals($exVariable,  $tag->getVariableName());
        $this->assertEquals($exDescription,  $tag->getDescription());
    }

    /**
     * Data provider for testConstructorParesInputsIntoCorrectFields
     *
     * @return array
     */
    public function provideDataForConstuctor()
    {
        // $type, $content
        return array(
            array(
                'var',
                'int',
                'int',
                '',
                ''
            ),
            array(
                'var',
                'int $bob',
                'int',
                '$bob',
                ''
            ),
            array(
                'var',
                'int $bob Number of bobs',
                'int',
                '$bob',
                'Number of bobs'
            ),
        );
    }
}
