<?php
declare(strict_types=1);

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

namespace phpDocumentor;

use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Event\PreFileEvent;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Application class for phpDocumentor.
 *
 * Can be used as bootstrap when the run method is not invoked.
 *
 * @codeCoverageIgnore too many side-effects and system calls to properly test
 */
class Application
{
    public static function VERSION(): string
    {
        return trim(file_get_contents(__DIR__ . '/../../VERSION'));
    }

    public static function templateDirectory(): string
    {
        $templateDir = __DIR__ . '/../../data/templates';

        // when installed using composer the templates are in a different folder
        $composerTemplatePath = __DIR__ . '/../../../templates';
        if (file_exists($composerTemplatePath)) {
            $templateDir = $composerTemplatePath;
        }

        return $templateDir;
    }

    /**
     * Initializes all components used by phpDocumentor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->defineIniSettings();

        Dispatcher::getInstance()->addListener(
            'parser.file.pre',
            function (PreFileEvent $event) use ($logger) {
                $logger->log(LogLevel::NOTICE, 'Parsing ' . $event->getFile());
            }
        );
    }

    /**
     * Adjust php.ini settings.
     *
     * @throws RuntimeException
     */
    protected function defineIniSettings(): void
    {
        $this->setTimezone();
        ini_set('memory_limit', '-1');

        if (extension_loaded('Zend OPcache') && ini_get('opcache.enable') && ini_get('opcache.enable_cli')) {
            if (ini_get('opcache.save_comments')) {
                ini_set('opcache.load_comments', '1');
            } else {
                ini_set('opcache.enable', '0');
            }
        }

        if (extension_loaded('Zend Optimizer+') && ini_get('zend_optimizerplus.save_comments') === 0) {
            throw new RuntimeException('Please enable zend_optimizerplus.save_comments in php.ini.');
        }
    }

    /**
     * If the timezone is not set anywhere, set it to UTC.
     *
     * This is done to prevent any warnings being outputted in relation to using
     * date/time functions.
     *
     * @link http://php.net/manual/en/function.date-default-timezone-get.php for more information how PHP determines the
     *     default timezone.
     */
    protected function setTimezone(): void
    {
        if (false === ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }
}
