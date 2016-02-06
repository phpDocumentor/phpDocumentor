<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Validation\Rule\Php;


use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Method;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Validation\Result;

/**
 * @coversDefaultClass phpDocumentor\Validation\Rule\Php\ParamRequired
 */
class ParamRequiredTest extends \PHPUnit_Framework_TestCase
{
    /** @var ParamRequired */
    private $fixture;

    /**
     *
     */
    protected function setUp()
    {
        $this->fixture = new ParamRequired();
    }

    /**
     * @covers ::validate
     */
    public function testUndocumentedMethod()
    {
        $result = new Result();
        $method = new Method(new Fqsen('\\' .static::class. '::' . __FUNCTION__ . '()'), new Visibility(Visibility::PUBLIC_));

        $this->fixture->validate($method, $result);
        $this->assertCount(0, $result->getViolations());
    }
}
