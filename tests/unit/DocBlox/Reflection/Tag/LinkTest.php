<?php
/**
 * DocBlox Link Tag Test
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Test class for DocBlox_Reflection_DocBlock_Tag_Link
 *
 * @category   DocBlox
 * @package    Reflection
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */
class DocBlox_Reflection_DocBlock_Tag_LinkTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test that the DocBlox_Reflection_DocBlock_Tag_Link can create a link
     * for the @link doc block
     *
     * @param string $type
     * @param string $content
     * @param string $exName
     * @param string $exContent
     * @param string $exDescription
     * @param string $exLink
     *
     * @covers DocBlox_Reflection_DocBlock_Tag_Link::__construct
     * @dataProvider provideDataForConstuctor
     *
     * @return void
     */
    public function testConstructorParesInputsIntoCorrectFields(
        $type, $content, $exName, $exContent, $exDescription, $exLink
    )
    {
        $tag = new DocBlox_Reflection_DocBlock_Tag_Link($type, $content);

        $actualName = $tag->getName();
        $actualContent = $tag->getContent();
        $actualDescription = $tag->getDescription();
        $actualLink = $tag->getLink();

        $this->assertEquals($exName, $actualName);
        $this->assertEquals($exContent, $actualContent);
        $this->assertEquals($exDescription, $actualDescription);
        $this->assertEquals($exLink, $actualLink);
    }

    /**
     * Data provider for testConstructorParesInputsIntoCorrectFields
     *
     * @return array
     */
    public function provideDataForConstuctor()
    {
        // $type, $content, $exName, $exContent, $exDescription, $exLink
        return array(
            array(
                'link',
                'http://www.docblox-project.org/',
                'link',
                'http://www.docblox-project.org/',
                'http://www.docblox-project.org/',
                'http://www.docblox-project.org/'
            ),
            array(
                'link',
                'http://www.docblox-project.org/ Testing',
                'link',
                'http://www.docblox-project.org/ Testing',
                'Testing',
                'http://www.docblox-project.org/'
            ),
            array(
                'link',
                'http://www.docblox-project.org/ Testing comments',
                'link',
                'http://www.docblox-project.org/ Testing comments',
                'Testing comments',
                'http://www.docblox-project.org/'
            ),
        );
    }
}