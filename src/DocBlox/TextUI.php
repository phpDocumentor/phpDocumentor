<?php
/**
 * DocBlox TextUI
 *
 * @category  DocBlox
 * @package   Base
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 */

/**
 * This class represents the UI aspect of the application
 *
 * @category  DocBlox
 * @package   Base
 * @copyright Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author    Ben Selby <benmatselby@gmail.com>
 */
class DocBlox_TextUI
{
    /**
     * Output the header
     *
     * @return void
     */
    public static function outputHeader()
    {
        echo 'DocBlox version ' . DocBlox_Core_Abstract::VERSION . PHP_EOL . PHP_EOL;
    }
}