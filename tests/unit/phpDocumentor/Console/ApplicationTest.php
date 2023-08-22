<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Console;

use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\EventDispatcher\EventDispatcher;

use function str_repeat;

/**
 * @coversDefaultClass \phpDocumentor\Console\Application
 * @covers ::__construct
 * @covers ::<private>
 */
final class ApplicationTest extends TestCase
{
    use ProphecyTrait;

    private Application $feature;

    public function setUp(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcher::class);
        $eventDispatcher->addListener(Argument::cetera(), Argument::cetera())->willReturn(null);
        $eventDispatcher->dispatch(Argument::cetera())->willReturnArgument(0);

        $this->feature = new Application(
            [(new Command('project:run'))->setCode(fn () => 2)],
            $eventDispatcher->reveal(),
            $this->prophesize(Logger::class)->reveal(),
        );
        $this->feature->setAutoExit(false);
    }

    /** @covers ::getCommandName */
    public function testWhetherTheNameOfTheCommandCanBeRetrieved(): void
    {
        $_SERVER['argv'] = ['binary', 'my:command'];
        $this->feature->add((new Command('my:command'))->setCode(fn () => 1));

        self::assertSame(1, $this->feature->run(new StringInput('my:command -q')));
    }

    /**
     * @link https://github.com/phpDocumentor/phpDocumentor/issues/3215
     *
     * @covers ::getCommandName
     */
    public function testCommandNamesLongerThanHundredCharactersAreIgnored(): void
    {
        $commandName = str_repeat('a', 101);
        $_SERVER['argv'] = ['binary', $commandName];
        $this->feature->add((new Command('my:command'))->setCode(fn () => 1));

        self::assertSame(2, $this->feature->run(new StringInput($commandName . ' -q')));
    }

    /**
     * @link https://github.com/phpDocumentor/phpDocumentor/issues/3215
     *
     * @covers ::getCommandName
     */
    public function testUnknownCommandNamesAreIgnored(): void
    {
        $_SERVER['argv'] = ['binary', 'unknown'];
        $this->feature->add((new Command('my:command'))->setCode(fn () => 1));

        self::assertSame(2, $this->feature->run(new StringInput('unknown -q')));
    }

    /** @covers ::getCommandName */
    public function testWhetherTheRunCommandIsUsedWhenNoCommandNameIsGiven(): void
    {
        $_SERVER['argv'] = ['binary', 'something else'];
        $this->feature->add((new Command('MyCommand'))->setCode(fn () => 1));

        self::assertSame(2, $this->feature->run(new StringInput('-q')));
    }

    /** @covers ::getDefaultInputDefinition */
    public function testWhetherTheConfigurationAndLogIsADefaultInput(): void
    {
        $definition = $this->feature->getDefinition();

        self::assertTrue($definition->hasOption('config'));
        self::assertTrue($definition->hasOption('log'));
    }

    /** @covers ::getLongVersion */
    public function testGetLongVersion(): void
    {
        self::assertMatchesRegularExpression(
            '~phpDocumentor <info>v(.*)</info>~',
            $this->feature->getLongVersion(),
        );
    }
}
