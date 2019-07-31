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
namespace phpDocumentor\Application\Stage\Parser;

use phpDocumentor\Parser\FileCollector;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class CollectFiles
{
    /**
     * @var FileCollector
     */
    private $fileCollector;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FileCollector $fileCollector, LoggerInterface $logger)
    {
        $this->fileCollector = $fileCollector;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload)
    {
        $this->log('Collecting files .. ');
        $apiConfig = $payload->getApiConfig();

        $ignorePaths = array_map(
            static function ($value) {
                if (substr((string) $value, -1) === '*') {
                    return substr($value, 0, -1);
                }

                return $value;
            },
            $apiConfig['ignore']['paths']
        );

        $files = $this->fileCollector->getFiles(
            $apiConfig['source']['dsn'],
            $apiConfig['source']['paths'],
            [
                'paths' => $ignorePaths,
                'hidden' => $apiConfig['ignore']['hidden'],
            ],
            $apiConfig['extensions']
        );
        $this->log('OK');

        return $payload->withFiles($files);
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     */
    private function log(string $message, string $priority = LogLevel::INFO, array $parameters = []): void
    {
        $this->logger->log($priority, $message, $parameters);
    }
}
