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

    /**
     * @var string
     */
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
        vfsStream::newFile('composer.json')->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalledCustomVendor() : void
    {
        $root = vfsStream::setup('root', null, $this->customVendorDir);
        vfsStream::newFile('composer.json')
            ->withContent($this->customVendorDirComposer)
            ->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }
}
