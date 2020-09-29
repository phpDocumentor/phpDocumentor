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
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use function getcwd;
use function is_dir;
use function strlen;

/**
 * @codeCoverageIgnore Kernels do not need to be covered; mostly configuration anyway
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

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
     */
    public function getWorkingDir() : string
    {
        return getcwd();
    }

    public function getCacheDir() : string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir() : string
    {
        if ($this->isPhar()) {
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
     *
     * @return string
     */
    public function getProjectDir()
    {
        return parent::getProjectDir();
    }

    public function registerBundles() : iterable
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $c, LoaderInterface $loader) : void
    {
        $c->setParameter('container.autowiring.strict_mode', true);
        $c->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';
        $loader->load($confDir . '/packages/*' . self::CONFIG_EXTS, 'glob');
        if (is_dir($confDir . '/packages/' . $this->environment)) {
            $loader->load($confDir . '/packages/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        }

        $loader->load($confDir . '/services' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/services_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes) : void
    {
        $confDir = $this->getProjectDir() . '/config';
        if (is_dir($confDir . '/routes/')) {
            $routes->import($confDir . '/routes/*' . self::CONFIG_EXTS, '/', 'glob');
        }

        if (is_dir($confDir . '/routes/' . $this->environment)) {
            $routes->import($confDir . '/routes/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        }

        $routes->import($confDir . '/routes' . self::CONFIG_EXTS, '/', 'glob');
    }

    public static function isPhar() : bool
    {
        return strlen(Phar::running()) > 0;
    }
}
