<?php
/**
 * File has multiple packages defined, which is incorrect
 *
 * @category   phpDocumentor
 * @package    Parser
 * @package    ParserPackage
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * This class has no @package tag but contains a @subpackage tag
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tests
 * @author     Ben Selby <benmatselby@gmail.com>
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class MultiplePackagesDocBlock
{
}
