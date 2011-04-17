<?php
/**
 * File contains the DocBlox_Core_Validator interface
 *
 * @category   DocBlox
 * @package    Parser
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */

/**
 * Interface for validation
 *
 * @category   DocBlox
 * @package    Parser
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
interface DocBlox_Parser_DocBlock_Validator
{
    /**
     * Constructor
     *
     * @param string                      $filename   Filename
     * @param int                         $lineNumber The line number for the docblock
     * @param DocBlox_Reflection_DocBlock $docblock   Docbloc
     */
    public function __construct($filename, $lineNumber, DocBlox_Reflection_DocBlock $docblock);

    /**
     * Is the docbloc valid
     *
     * @return boolean
     */
    public function isValid();
}