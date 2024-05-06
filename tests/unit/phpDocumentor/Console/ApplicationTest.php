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

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

use function str_repeat;

/**
 * @coversDefaultClass \phpDocumentor\Console\Application
 * @covers ::__construct
 
 */
final class ApplicationTest extends TestCase
{
    use ProphecyTrait;

    private Application $feature;

    public function setUp(): void
    {
        $this->feature = new Application();
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

        self::assertSame(
            1,
            $this->feature->run(new ArrayInput(['command_name' => $commandName]), new BufferedOutput()),
        );
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

        self::assertSame(1, $this->feature->run(new StringInput('unknown -q'), new BufferedOutput()));
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
