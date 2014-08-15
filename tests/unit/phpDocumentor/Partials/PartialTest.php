<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Partials;

class PartialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Partials\Partial::getContent
     * @covers phpDocumentor\Partials\Partial::setContent
     */
    public function testGetContent(){

        $expect = new Partial;
        $expect->setContent('Foo bar');
        $expectContent = $expect->getContent();

        $this->assertEquals($expectContent, 'Foo bar');
    }

    /**
     * @covers phpDocumentor\Partials\Partial::getLink
     * @covers phpDocumentor\Partials\Partial::setLink
     */
    public function testGetLink(){

        $expect = new Partial;
        $expect->setLink('http://www.phpdoc.org/');
        $expectContent = $expect->getLink();

        $this->assertEquals($expectContent, 'http://www.phpdoc.org/');
    }

    /**
     * @covers phpDocumentor\Partials\Partial::getName
     * @covers phpDocumentor\Partials\Partial::setName
     */
    public function testGetName(){

        $expect = new Partial;
        $expect->setName('My name');
        $expectContent = $expect->getName();

        $this->assertEquals($expectContent, 'My name');
    }
}
?>