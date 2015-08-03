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

namespace phpDocumentor\Renderer\Router;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Renderer\Router\Rule::__construct
     * @covers phpDocumentor\Renderer\Router\Rule::match
     */
    public function testIfRuleCanBeMatched()
    {
        $fixture = new Rule(
            function () {
                return true;
            },
            function () {
            }
        );
        $fixture2 = new Rule(
            function () {
                return false;
            },
            function () {
            }
        );

        $node = 'test';
        $this->assertTrue($fixture->match($node));
        $this->assertFalse($fixture2->match($node));
    }

    /**
     * @covers phpDocumentor\Renderer\Router\Rule::__construct
     * @covers phpDocumentor\Renderer\Router\Rule::generate
     */
    public function testIfUrlCanBeGenerated()
    {
        $fixture = new Rule(
            function () {
            },
            function () {
                return 'url';
            }
        );

        $this->assertSame('url', $fixture->generate('test'));
    }

    /**
     * @covers phpDocumentor\Renderer\Router\Rule::__construct
     * @covers phpDocumentor\Renderer\Router\Rule::generate
     * @covers phpDocumentor\Renderer\Router\Rule::translateToUrlEncodedPath
     */
    public function testTranslateToUrlEncodedPath()
    {
        $fixture = new Rule(
            function () {
                return true;
            },
            function () {
                return 'httö://www.€xample.org/foo.html#bär';
            }
        );

        $this->assertSame('httö://www.EURxample.org/foo.html#bär', $fixture->generate('test'));
    }
}
