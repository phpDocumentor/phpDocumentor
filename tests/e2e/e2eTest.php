<?php

declare(strict_types=1);

namespace e2e;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class e2eTest extends TestCase
{
    /** @return iterable<string, array{command: string[], stdout: string|false, stderr: string, cwd?: string}> */
    public static function provider(): iterable
    {
        yield "help" => [
            "command" => ["help"],
            "stdout" => file_get_contents(__DIR__ . "/tests/help/stdout.txt"),
            "stderr" => "",
        ];
        yield "run --help" => [
            "command" => ["run", "--help"],
            "stdout" => file_get_contents(__DIR__ . "/tests/help/run_stdout.txt"),
            "stderr" => "",
        ];
        yield "version" => [
            "command" => ["--version"],
            "stdout" => file_get_contents(__DIR__ . "/tests/version/stdout.txt"),
            "stderr" => "",
        ];
        yield "run small project" => [
            "command" => ["--no-progress"],
            "stdout" => file_get_contents(__DIR__ . "/tests/small_project/stdout.txt"),
            "stderr" => "",
            "cwd" => __DIR__ . "/tests/small_project",
        ];
        yield "run small project with -d" => [
            "command" => [
                "--no-progress",
                "-d",
                "./tests/small_project/src"
            ],
            "stdout" => file_get_contents(__DIR__ . "/tests/small_project/stdout.txt"),
            "stderr" => "",
        ];
        yield "run project with extensions" => [
            "command" => [
                "--no-progress",
            ],
            "stdout" => file_get_contents(__DIR__ . "/tests/project_with_extensions/stdout.txt"),
            "stderr" => "",
            "cwd" => __DIR__ . "/tests/project_with_extensions",
        ];
        yield "run project with extensions disable extensions" => [
            "command" => [
                "--no-progress",
                "--no-extensions",
            ],
            "stdout" => file_get_contents(__DIR__ . "/tests/project_with_extensions/no_extensions_stdout.txt"),
            "stderr" => "",
            "cwd" => __DIR__ . "/tests/project_with_extensions",
        ];
        yield "run project with composer installed extensions" => [
            "command" => [
                "--no-progress",
            ],
            "stdout" => file_get_contents(__DIR__ . "/tests/project_with_vendor_extensions/stdout.txt"),
            "stderr" => "",
            "cwd" => __DIR__ . "/tests/project_with_vendor_extensions",
        ];
    }

    /** @param string[] $command */
    #[DataProvider("provider")]
    #[TestDox('run phpdoc')]
    public function testCli(
        array $command,
        string $stdout,
        string $stderr,
        string $cwd = __DIR__
    ): void {
        $process = new Process(
            [
                "phpdoc", ...$command
            ],
            $cwd,
            ['PATH' => realpath(__DIR__ . '/../../bin') . ':'. $_ENV['PATH']]
        );
        $statusCode = $process->run();

        self::assertStringMatchesFormat($stdout, $process->getOutput());
        self::assertStringMatchesFormat($stderr, $process->getErrorOutput());
        self::assertEquals(0, $statusCode);
    }
}
