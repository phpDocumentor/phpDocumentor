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

namespace phpDocumentor;

use Phar;
use phpDocumentor\DependencyInjection\GuidesCommandsPass;
use phpDocumentor\DependencyInjection\ReflectionProjectFactoryStrategyPass;
use phpDocumentor\Guides\DependencyInjection\GuidesExtension;
use phpDocumentor\Guides\RestructuredText\DependencyInjection\ReStructuredTextExtension;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Webmozart\Assert\Assert;

use function getcwd;
use function strlen;

/** @codeCoverageIgnore Kernels do not need to be covered; mostly configuration anyway */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    final public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * Returns the current working directory.
     *
     * By default, symfony does not track the current working directory. Since we want to use this information to
     * locate certain resources, such as the configuration files, we add a new method in the kernel that can be used
     * as an expression to be passed to service definitions.
     *
     * For example:
     *
     * ```
     *     phpDocumentor\Configuration\ConfigurationFactory:
     *       arguments:
     *         $defaultFiles:
     *           - "@=service('kernel').getWorkingDir() ~ '/phpdoc.xml'"
     *           - "@=service('kernel').getWorkingDir() ~ '/phpdoc.dist.xml'"
     *           - "@=service('kernel').getWorkingDir() ~ '/phpdoc.xml.dist'"
     * ```
     *
     * @noinspection PhpUnused this method is used in the services.yaml and the inspection does not pick this up.
     */
    public function getWorkingDir(): string
    {
        $workingDirectory = getcwd();
        Assert::stringNotEmpty($workingDirectory);

        return $workingDirectory;
    }

    public function getCacheDir(): string
    {
        if (isset($_SERVER['APP_CACHE_DIR'])) {
            return $_SERVER['APP_CACHE_DIR'] . '/' . $this->environment;
        }

        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        if (isset($_SERVER['APP_LOG_DIR'])) {
            return $_SERVER['APP_LOG_DIR'];
        }

        if (self::isPhar()) {
            return '/tmp/php-doc/log';
        }

        return $this->getProjectDir() . '/var/log';
    }

    /**
     * Override needed for auto-detection when installed using Composer.
     *
     * I am not quite sure why, but without this overridden method Symfony will use the 'src' directory as Project Dir
     * when phpDocumentor is installed using Composer. Without being installed with composer it works fine without
     * this hack.
     */
    public function getProjectDir(): string
    {
        return parent::getProjectDir();
    }

    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) === false && isset($envs[$this->environment]) === false) {
                continue;
            }

            yield new $class();
        }
    }

    public function build(ContainerBuilder $container): void
    {
        $container->setParameter('vendor_dir', AutoloaderLocator::findVendorPath());
        $container->addCompilerPass(new ReflectionProjectFactoryStrategyPass());
        $container->addCompilerPass(new GuidesCommandsPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 20);

        $guides = new GuidesExtension();
        $rst = new ReStructuredTextExtension();
        $container->registerExtension($guides);
        $container->registerExtension($rst);
        $container->addCompilerPass($guides, PassConfig::TYPE_BEFORE_OPTIMIZATION, 20);
        $container->addCompilerPass($rst);

        $container->loadFromExtension($guides->getAlias());
        $container->loadFromExtension($rst->getAlias());
    }

    public static function isPhar(): bool
    {
        return strlen(Phar::running()) > 0;
    }
}
