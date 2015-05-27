<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Project\Version;


use phpDocumentor\Project\VersionNumber;

class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testVersionNumber()
    {
        $definition = new Definition(new VersionNumber('1.0.1'));

        $this->assertEquals(new VersionNumber('1.0.1'), $definition->getVersionNumber());
    }
}
