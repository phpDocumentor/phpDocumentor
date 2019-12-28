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
use RuntimeException;
use function putenv;

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
    private $composerInstalledStructureCustomVendorDir = [
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

    /**
     * Directory structure when phpdocumentor is installed from git.
     *
     * @var array
     */
    private $standaloneStructureCustomVendorDir = [
        'dummy' => [
            'custom-vendor' => [],
            'src' => [
                'phpDocumentor' => [],
            ],
            'test' => [],
        ],
    ];

    public function testAutoloadStandaloneInstalledDefaultVendor() : void
    {
        vfsStream::setup('root', null, $this->standaloneStructure);
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadStandaloneInstalledCustomVendorEnvironmentVar() : void
    {
        putenv('COMPOSER_VENDOR_DIR=custom-vendor');
        vfsStream::setup('root', null, $this->standaloneStructureCustomVendorDir);
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
        putenv('COMPOSER_VENDOR_DIR');
    }

    public function testAutoloadStandaloneInstalledCustomVendorConfigurationEntry() : void
    {
        $root = vfsStream::setup('root', null, $this->standaloneStructureCustomVendorDir);
        vfsStream::newFile('composer.json')
            ->withContent('{"config": {"vendor-dir": "custom-vendor"}}')
            ->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadStandaloneInstalledCustomVendorConfigurationEntryOverridenByEnvironment() : void
    {
        putenv('COMPOSER_VENDOR_DIR=custom-vendor');
        $root = vfsStream::setup('root', null, $this->standaloneStructureCustomVendorDir);
        vfsStream::newFile('composer.json')
            ->withContent('{"config": {"vendor-dir": "overridden-custom-vendor"}}')
            ->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
        putenv('COMPOSER_VENDOR_DIR');
    }

    public function testAutoloadStandaloneInstalledCustomVendorCustomConfigurationEntry() : void
    {
        putenv('COMPOSER=custom_composer');
        $root = vfsStream::setup('root', null, $this->standaloneStructureCustomVendorDir);
        vfsStream::newFile('custom_composer.json')
            ->withContent('{"config": {"vendor-dir": "custom-vendor"}}')
            ->at($root->getChild('dummy'));
        $baseDir = vfsStream::url('root/dummy/src/phpDocumentor');
        self::assertSame(
            'vfs://root/dummy/src/phpDocumentor/../../custom-vendor',
            AutoloaderLocator::findVendorPath($baseDir)
        );
        putenv('COMPOSER');
    }

    public function testAutoloadComposerInstalled() : void
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructure);
        vfsStream::newFile('autoload.php')->at($root->getChild('dummy')->getChild('vendor'));
        $baseDir = vfsStream::url('root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalledCustomVendor() : void
    {
        $root = vfsStream::setup('root', null, $this->composerInstalledStructureCustomVendorDir);
        vfsStream::newFile('autoload.php')->at($root->getChild('dummy')->getChild('custom-vendor'));
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../',
            AutoloaderLocator::findVendorPath($baseDir)
        );
    }

    public function testAutoloadComposerInstalledCustomVendorEnvironmentVar() : void
    {
        putenv('COMPOSER_VENDOR_DIR=custom-vendor');
        $root = vfsStream::setup('root', null, $this->composerInstalledStructureCustomVendorDir);
        vfsStream::newFile('autoload.php')->at($root->getChild('dummy')->getChild('custom-vendor'));
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->assertSame(
            'vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor/../../../../',
            AutoloaderLocator::findVendorPath($baseDir)
        );
        putenv('COMPOSER_VENDOR_DIR');
    }

    public function testAutoloadComposerNotFindableVendor() : void
    {
        $root = vfsStream::setup('root', null, []);
        $baseDir = vfsStream::url('root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor');
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to find vendor directory for '
            . 'vfs://root/dummy/custom-vendor/phpDocumentor/phpDocumentor/src/phpDocumentor'
        );
        AutoloaderLocator::findVendorPath($baseDir);
    }
}
