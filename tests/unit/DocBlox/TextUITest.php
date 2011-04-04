<?php
/**
 * DocBlox TextUI
 *
 * @category  DocBlox
 * @package   Tests
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 */

/**
 * Testing class for DocBlox_TextUI which represents the UI for the application
 *
 * @category  DocBlox
 * @package   Tests
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 */
class DocBlox_TextUITest extends PHPUnit_Extensions_OutputTestCase
{
    /** 
     * Test that the TextUI can output the version number correctly
     * rather than having each task define the output
     *
     * @covers DocBlox_TextUI::outputHeader
     *
     * @return void
     */
    public function testTextUICanOutputVersionNumber()
    {
        $this->expectOutputString('DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL);
        DocBlox_TextUI::outputHeader();
    }
}