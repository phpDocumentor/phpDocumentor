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

namespace PhpDocumentorStandard\Tests\Commenting;

require_once __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php';
require_once __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/src/Util/Tokens.php';

if (defined('PHP_CODESNIFFER_CBF') === false) {
    define('PHP_CODESNIFFER_CBF', false);
}

if (defined('PHP_CODESNIFFER_VERBOSITY') === false) {
    define('PHP_CODESNIFFER_VERBOSITY', 0);
}

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Files\DummyFile;
use PHP_CodeSniffer\Ruleset;
use PHPUnit\Framework\TestCase;

use function defined;
use function define;
use function end;
use function explode;
use function preg_replace;

final class FileHeaderSniffTest extends TestCase
{
    private const CANONICAL_HEADER = <<<'EOT'
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */
EOT;

    private const STANDARD_DIR = __DIR__ . '/../..';

    /** @return iterable<string, array{string, list<string>}> */
    public static function provideScenarios(): iterable
    {
        yield 'canonical header passes' => [
            "<?php\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n",
            [],
        ];

        yield 'missing header before namespace' => [
            "<?php\n\ndeclare(strict_types=1);\n\nnamespace Acme;\n\nfinal class Foo {}\n",
            ['Missing'],
        ];

        yield 'missing header without declare' => [
            "<?php\n\nnamespace Acme;\n\nfinal class Foo {}\n",
            ['Missing'],
        ];

        yield 'header attached to class (no namespace) is reported as missing' => [
            "<?php\n\ndeclare(strict_types=1);\n\n/**\n * class doc\n */\nfinal class Foo {}\n",
            ['Missing'],
        ];

        yield 'invalid header content is reported and fixable' => [
            "<?php\n\ndeclare(strict_types=1);\n\n/**\n * Wrong content.\n */\n\nnamespace Acme;\n",
            ['Invalid'],
        ];

        yield 'canonical header before attribute passes (no duplication)' => [
            "<?php\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\n#[\\Attribute]\nfinal class Foo {}\n",
            [],
        ];

        yield 'canonical header before namespace with leading comment passes' => [
            "<?php\n\n// shebang-like comment\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n",
            [],
        ];

        yield 'multi-declare with canonical header passes' => [
            "<?php\n\ndeclare(strict_types=1);\n\ndeclare(ticks=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n",
            [],
        ];

        yield 'block-form declare without header is reported missing' => [
            "<?php\n\ndeclare(strict_types=1) {\n    namespace Acme;\n}\n",
            ['Missing'],
        ];

        yield 'crlf line endings in canonical header pass' => [
            preg_replace(
                '/\n/',
                "\r\n",
                "<?php\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n",
            ),
            [],
        ];
    }

    /**
     * @param list<string> $expectedCodes
     *
     * @dataProvider provideScenarios
     */
    public function testSniffReportsExpectedViolations(string $source, array $expectedCodes): void
    {
        $config = new Config([], false);
        $config->standards = [self::STANDARD_DIR];
        $config->sniffs = ['PhpDocumentorStandard.Commenting.FileHeader'];

        $ruleset = new Ruleset($config);

        $file = new DummyFile($source, $ruleset, $config);
        $file->process();

        $reported = [];
        foreach ($file->getErrors() as $lineErrors) {
            foreach ($lineErrors as $columnErrors) {
                foreach ($columnErrors as $error) {
                    $parts = explode('.', $error['source']);
                    $reported[] = end($parts);
                }
            }
        }

        self::assertSame($expectedCodes, $reported, 'Unexpected set of reported error codes.');
    }

    public function testFixerProducesCanonicalHeaderForMissing(): void
    {
        $source = "<?php\n\ndeclare(strict_types=1);\n\nnamespace Acme;\n\nfinal class Foo {}\n";
        $expected = "<?php\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n\nfinal class Foo {}\n";

        self::assertSame($expected, $this->fix($source));
    }

    public function testFixerReplacesInvalidHeader(): void
    {
        $source = "<?php\n\ndeclare(strict_types=1);\n\n/**\n * wrong\n */\n\nnamespace Acme;\n";
        $expected = "<?php\n\ndeclare(strict_types=1);\n\n" . self::CANONICAL_HEADER . "\n\nnamespace Acme;\n";

        self::assertSame($expected, $this->fix($source));
    }

    public function testFixerIsIdempotent(): void
    {
        $source = "<?php\n\ndeclare(strict_types=1);\n\nnamespace Acme;\n";
        $firstPass = $this->fix($source);
        $secondPass = $this->fix($firstPass);

        self::assertSame($firstPass, $secondPass);
    }

    private function fix(string $source): string
    {
        $config = new Config([], false);
        $config->standards = [self::STANDARD_DIR];
        $config->sniffs = ['PhpDocumentorStandard.Commenting.FileHeader'];

        $ruleset = new Ruleset($config);

        $file = new DummyFile($source, $ruleset, $config);
        $file->process();
        $file->fixer->fixFile();

        return $file->fixer->getContents();
    }
}
