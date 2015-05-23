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

namespace phpDocumentor\Console\Output;

use Mockery as m;

/**
 * Tests whether the utility functions for writing to stdOut work.
 */
class OutputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Console\Output\Output::writeTimedLog
     */
    public function testWriteTimedLog()
    {
        $output = new Output();
        $stream = fopen('php://memory', 'a', false);

        // because the ConsoleOutput base class disables injecting the stream and uses php://stdout we need to crack
        // open the class and inject our own stream.(php://stdout cannot be captured with output buffering)
        $reflectedStream = new \ReflectionProperty('Symfony\Component\Console\Output\StreamOutput', 'stream');
        $reflectedStream->setAccessible(true);
        $reflectedStream->setValue($output, $stream);

        $suite = $this;

        $output->writeTimedLog(
            str_repeat('1', 80),
            function ($operation, $arguments) use ($suite) {
                $suite->assertSame('Foo', $operation);
                $suite->assertSame('Bar', $arguments);
            },
            array('Foo', 'Bar')
        );

        rewind($stream);
        $this->assertRegExp('/^[1]{66} .. [\ 0-9\.]{8}s\n$/', stream_get_contents($stream));
    }

    public function testWriteLogger()
    {
        $output = new Output();
        $stream = fopen('php://memory', 'a', false);

        // because the ConsoleOutput base class disables injecting the stream and uses php://stdout we need to crack
        // open the class and inject our own stream.(php://stdout cannot be captured with output buffering)
        $reflectedStream = new \ReflectionProperty('Symfony\Component\Console\Output\StreamOutput', 'stream');
        $reflectedStream->setAccessible(true);
        $reflectedStream->setValue($output, $stream);

        $output->write('test');

        rewind($stream);
        $this->assertRegExp('/^test$/', stream_get_contents($stream));
    }
}
