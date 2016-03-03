<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application;

use Mockery as m;
use Symfony\Component\Console\Application as ConsoleApplication;

/**
 * @coversDefaultClass phpDocumentor\Application\Application
 * @covers ::<private>
 */
final class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldUseTheSystemTimeZone()
    {
        ini_set('date.timezone', 'Europe/Amsterdam');
        new Application();

        $this->assertSame('Europe/Amsterdam', date_default_timezone_get());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldSetADefaultTimeZoneIfNoneIsPresent()
    {
        @ini_set('date.timezone', false);
        new Application();

        $this->assertSame('UTC', date_default_timezone_get());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldRemoveTheMemoryLimit()
    {
        ini_set('memory_limit', 500);
        new Application();
        $this->assertSame('-1', ini_get('memory_limit'));
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itShouldDisableGarbageCollection()
    {
        gc_enable();
        new Application();
        $this->assertFalse(gc_enabled());
    }

    /**
     * @test
     * @covers ::run
     */
    public function itShouldStartTheConsoleApplicationWhenRan()
    {
        $consoleApplication = m::mock(ConsoleApplication::class);
        $consoleApplication->shouldReceive('setAutoExit')->once()->with(false);
        $consoleApplication->shouldReceive('run')->once();

        $application = new Application([], [[ConsoleApplication::class => $consoleApplication]]);
        $application->run();
    }
}
