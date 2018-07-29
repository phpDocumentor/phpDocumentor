<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 *
 *
 */

namespace phpDocumentor\Application\Console;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \phpDocumentor\Application\Console\Application
 * @covers ::<private>
 */
class ApplicationTest extends MockeryTestCase
{
    /**
     * @covers ::__construct
     * @covers ::getLongVersion
     */
    public function testGetLongVersion(): void
    {
        $kernelMock = m::mock(KernelInterface::class);
        $kernelMock->shouldIgnoreMissing();
        $feature = new Application($kernelMock);

       self::assertRegExp('~phpDocumentor <info>v(\d).(\d).(\d|x)?-(.*)</info>~',  $feature->getLongVersion());
    }
}
