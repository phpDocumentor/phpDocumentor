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

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Parser\Middleware\ReEncodingMiddleware;
use phpDocumentor\Parser\Parser;
use Psr\Log\LoggerInterface;

final class ParseFiles
{
    public function __construct(
        private readonly Parser $parser,
        private readonly LoggerInterface $logger,
        private readonly ReEncodingMiddleware $reEncodingMiddleware,
    ) {
    }

    public function __invoke(ApiSetPayload $payload): ApiSetPayload
    {
        $apiConfig = $payload->getApiSet()->getSettings();

        $builder = $payload->getBuilder();
        $builder->usingApiSpecification($apiConfig);
        $builder->usingDefaultPackageName($apiConfig['default-package-name'] ?? '');

        // TODO: The setVisibility call should purge the cache if it differs; but once we are here, cache has already
        //       been loaded..
        $payload->getBuilder()->setVisibility($apiConfig->calculateVisiblity());

        $encoding = $apiConfig['encoding'] ?? '';
        if ($encoding) {
            $this->reEncodingMiddleware->withEncoding($encoding);
        }

        $this->parser->setMarkers($apiConfig['markers'] ?? []);
        $this->parser->setValidate(($apiConfig['validate'] ?? 'false') === 'true');
        $this->parser->setDefaultPackageName($builder->getDefaultPackageName());

        $this->logger->notice('Parsing files');
        $payload->getBuilder()->populateApiDocumentationSet(
            $payload->getApiSet(),
            $this->parser->parse($payload->getFiles()),
        );

        return $payload;
    }
}
