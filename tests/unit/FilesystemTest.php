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

namespace phpDocumentor;

/**
 * Test case for checking the project's filesystem.
 * @coversNothing
 */
final class FilesystemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Maximum filepath length for Windows.
     */
    const WINDOWS_MAX_FILEPATH_LENGTH = 260;

    public function testFilenamesShouldNotContainNonASCIICharacters()
    {
        $iterator = new \RecursiveDirectoryIterator(__DIR__ . '/../../');
        $iterator = new \RecursiveIteratorIterator($iterator);

        $nonASCIIFilenames = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!mb_detect_encoding($file->getPathname(), 'ASCII', true)) {
                $nonASCIIFilenames[] = $file->getPathname();
            }
        }

        $this->assertCount(
            0,
            $nonASCIIFilenames,
            'Found ' . count($nonASCIIFilenames) . ' filenames that contain non-ASCII characters:'
            . PHP_EOL . print_r($nonASCIIFilenames, true)
        );
    }

    public function testFilenamesShouldNotContainInvalidCharactersForWindows()
    {
        /** The following characters are invalid for Windows:
         * < (less than)
         * > (greater than)
         * : (colon)
         * " (double quote)
         * / (forward slash)
         * \ (backslash)
         * | (vertical bar or pipe)
         * ? (question mark)
         * * (asterisk)
         */
        $invalidCharacters = '/[\<\>\:\"\/\|\?\*]/';

        $iterator = new \RecursiveDirectoryIterator(__DIR__ . '/../../');
        $iterator = new \RecursiveIteratorIterator($iterator);

        $invalidDirectories = [];
        $invalidFilenames   = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            $directories = explode(DIRECTORY_SEPARATOR, $file->getPath());
            foreach ($directories as $directory) {
                if (preg_match($invalidCharacters, $directory)) {
                    $invalidDirectories[] = $directory;
                }
            }

            if (preg_match($invalidCharacters, $file->getFilename())) {
                $invalidFilenames[] = $file->getFilename();
            }
        }

        $this->assertCount(
            0,
            $invalidDirectories,
            'Found ' . count($invalidDirectories) . ' directories that contain invalid characters for Windows:'
            . PHP_EOL . print_r($invalidDirectories, true)
        );
        $this->assertCount(
            0,
            $invalidFilenames,
            'Found ' . count($invalidCharacters) . ' filenames that contain invalid characters for Windows:' . PHP_EOL
            . print_r($invalidFilenames, true)
        );
    }

    public function testFilepathShouldNotExceedMaximumPathLengthForWindows()
    {
        // Safe margin to account for filepaths longer than the current one.
        $safeMargin = count(realpath(__DIR__ . '/../../'));

        $iterator = new \RecursiveDirectoryIterator(__DIR__ . '/../../');
        $iterator = new \RecursiveIteratorIterator($iterator);

        $filepathTooLong = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (strlen(realpath($file->getPathname())) > self::WINDOWS_MAX_FILEPATH_LENGTH - $safeMargin) {
                $filepathTooLong[count($filepathTooLong)] = [
                    'filepath' => realpath($file->getPathname()),
                    'length'   => strlen(realpath($file->getPathname())),
                ];
            }
        }

        $this->assertCount(
            0,
            $filepathTooLong,
            'Found ' . count($filepathTooLong)
            . ' filepaths that potentially exceed the maximum path length for Windows:' . PHP_EOL
            . print_r($filepathTooLong, true)
        );
    }
}
