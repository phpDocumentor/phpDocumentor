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
 */

namespace phpDocumentor\Console;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * @coversDefaultClass \phpDocumentor\Console\Application
 * @covers ::__construct
 * @covers ::<private>
 */
class ApplicationTest extends MockeryTestCase
{
    /** @var Application */
    private $feature;

    public function setUp()
    {
        $kernelMock = m::mock(KernelInterface::class);
        $kernelMock->shouldIgnoreMissing();
        $kernelMock->shouldReceive('getBundles')->andReturn([]);
        $kernelMock->shouldReceive('getContainer->has')->andReturn(false);
        $kernelMock->shouldReceive('getContainer->hasParameter')->andReturn(false);
        $kernelMock->shouldReceive('getContainer->get')
            ->with('event_dispatcher')
            ->andReturn(new EventDispatcher());

        $kernelMock->shouldReceive('getContainer->get')->andReturn(false);

        $this->feature = new Application($kernelMock);
        $this->feature->setAutoExit(false);
    }

    /**
     * @covers ::getCommandName
     */
    public function testWhetherTheNameOfTheCommandCanBeRetrieved()
    {
        $_SERVER['argv'] = ['binary', 'my:command'];
        $this->feature->add((new Command('my:command'))->setCode(function () {
            return 1;
        }));
        $this->feature->add((new Command('project:run'))->setCode(function () {
            return 2;
        }));

        $this->assertSame(1, $this->feature->run(new StringInput('my:command -q')));
    }

    /**
     * @covers ::getCommandName
     */
    public function testWhetherTheRunCommandIsUsedWhenNoCommandNameIsGiven()
    {
        $_SERVER['argv'] = ['binary', 'something else'];
        $this->feature->add((new Command('MyCommand'))->setCode(function () {
            return 1;
        }));
        $this->feature->add((new Command('project:run'))->setCode(function () {
            return 2;
        }));

        $this->assertSame(2, $this->feature->run(new StringInput('-q')));
    }

    /**
     * @covers ::getDefaultInputDefinition
     */
    public function testWhetherTheConfigurationAndLogIsADefaultInput()
    {
        $definition = $this->feature->getDefinition();

        $this->assertTrue($definition->hasOption('config'));
        $this->assertTrue($definition->hasOption('log'));
    }

    /**
     * @covers ::getLongVersion
     */
    public function testGetLongVersion(): void
    {
        self::assertRegExp(
            '~phpDocumentor <info>v(.*)</info>~',
            $this->feature->getLongVersion()
        );
    }
}
