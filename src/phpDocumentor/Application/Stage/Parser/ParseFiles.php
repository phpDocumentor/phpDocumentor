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

use phpDocumentor\Parser\Parser;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final class ParseFiles
{
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(Parser $parser, LoggerInterface $logger)
    {
        $this->parser = $parser;
        $this->logger = $logger;
    }

    public function __invoke(Payload $payload)
    {
        $configuration = $payload->getConfig();
        $apiConfig = $payload->getApiConfig();

        $builder = $payload->getBuilder();
        $builder->setVisibility($apiConfig);
        $builder->setMarkers($apiConfig['markers']);
        $builder->setIncludeSource($apiConfig['include-source']);

        $this->parser->setForced(!$configuration['phpdocumentor']['use-cache']);

        if ($builder->getProjectDescriptor()->getSettings()->isModified()) {
            $this->parser->setForced(true);
            $this->log(
                'One of the project\'s settings have changed, forcing a complete rebuild',
                LogLevel::NOTICE
            );
        }

        $this->parser->setEncoding($apiConfig['encoding']);
        $this->parser->setMarkers($apiConfig['markers']);
        $this->parser->setIgnoredTags($apiConfig['ignore-tags']);
        $this->parser->setValidate($apiConfig['validate']);
        $this->parser->setDefaultPackageName($apiConfig['default-package-name']);

        $this->log('Parsing files', LogLevel::NOTICE);
        $project = $this->parser->parse($payload->getFiles());
        $payload->getBuilder()->build($project);

        return $payload;
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
