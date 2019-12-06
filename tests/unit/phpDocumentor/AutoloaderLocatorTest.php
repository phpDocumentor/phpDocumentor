<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

final class AutoloaderLocatorTest extends TestCase
{
    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    private $composerInstalledStructure = [
        'dummy' => [
            'vendor' => [
                'phpDocumentor' => [
                    'phpDocumentor' => [
                        'src' => [
                            'phpDocumentor' => [],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * Directory structure when phpdocumentor is installed using composer.
     *
     * @var array
     */
    private $customVendorDir = [
        'dummy' => [
            'custom-vendor' => [
                'phpDocumentor' => [
                    'phpDocumentor' => [
                        'src' => [
                            'phpDocumentor' => [],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /** @var string */
    private $customVendorDirComposer = '{
    "config": {
        "vendor-dir": "custom-vendor"
    }
    }';


    /**
     * Directory structure when phpdocumentor is installed from git.
     *
     * @var array
     */
    private $standaloneStructure = [
        'dummy' => [
            'vendor' => [],
            'src' => [
                'phpDocumentor' => [],
            ],
            'test' => [],
        ],
    ];

    public function testAutoloadAtDefaultLocation() : void
    {
        vfsStream::setup('root', null, $this->standaloneStructure);
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalled() : void
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructure);
        vfsStream::newFile('autoload.php')->at($root->getChild('dummy')->getChild('vendor'));
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalledCustomVendor() : void
    {
        $root = vfsStream::setup('root', null, $this->customVendorDir);
        vfsStream::newFile('autoload.php')->at($root->getChild('dummy')->getChild('custom-vendor'));
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerNotFindableVendor() : void
    {
        $root = vfsStream::setup('root', null, []);
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to find vendor directory for vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        AutoloaderLocator::findVendorPath($baseDir);
    }
}

/**
 * This function overrides the native realpath($url) function, removing
 * all the "..", ".", "///" of an url. Contrary to the native one, 
 * 
 * @see https://github.com/bovigo/vfsStream/issues/207
 * @param string $url
 * @param string|bool The cleaned url or false if it doesn't exist
 */
function realpath(string $url)
{
    if (preg_match("|^(\w+://)?(/)?(.*)$|", $url, $matches)) {
        $protocol = $matches[1];
        $root     = $matches[2];
        $rest     = $matches[3];
    }
    
    $split = preg_split("|/|", $rest);

    $cleaned = [];
    foreach ($split as $item) {
        if ($item === '.' || $item === '') {
            // If it's a ./ then it's nothing (just that dir) so don't add/delete anything
        } elseif ($item === '..') {
            // Remove the last item added since .. negates it.
            $removed = array_pop($cleaned);
        } else {
            $cleaned[] = $item;
        }
    }

    $cleaned = $protocol.$root.implode('/', $cleaned);
    return file_exists($cleaned) ? $cleaned : false;
}
