<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Console;

use PackageVersions\Versions;
use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\HttpKernel\KernelInterface;

final class Application extends BaseApplication
{
    const VERSION = '@package_version@';

    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->setName('phpDocumentor');
        $this->setVersion($this->detectVersion());
        $this->setDefaultCommand('project:run');
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion(): string
    {
        return sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
    }

    private function detectVersion(): string
    {
        $version = static::VERSION;
        if (static::VERSION === '@' . 'package_version' . '@') { //prevent replacing the version.
            $version = trim(file_get_contents(__DIR__ . '/../../../../VERSION'));
            try {
                $version = 'v' . \Jean85\PrettyVersions::getVersion(Versions::ROOT_PACKAGE_NAME)->getPrettyVersion();
            } catch (\OutOfBoundsException $e) {
            }
        }
        return $version;
    }
}
