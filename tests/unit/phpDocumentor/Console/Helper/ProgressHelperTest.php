<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Helper;

class ProgressHelperTest extends \PHPUnit_Framework_TestCase{
    
    public function testToSetTimeInHumanReadableText(){
        $time = new ProgressHelper();
        
        $this->assertEquals('2', 'humaneTime', $time);
    }
    
    public function testIfICanGetTheName(){
        $name = new ProgressHelper();
        
        $getName = $name->getName();
        
        $this->assertEquals('progress', $getName);
    }
}
?>