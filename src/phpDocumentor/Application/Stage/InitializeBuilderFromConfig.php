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

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Descriptor\Collection as PartialsCollection;

final class InitializeBuilderFromConfig
{
    /** @var PartialsCollection */
    private $partials;

    public function __construct(PartialsCollection $partials)
    {
        $this->partials = $partials;
    }

    public function __invoke(Payload $payload) : Payload
    {
        $configuration = $payload->getConfig();

        $builder = $payload->getBuilder();
        $builder->createProjectDescriptor();
        $builder->setName($configuration['phpdocumentor']['title'] ?? '');
        $builder->setPartials($this->partials);

        return $payload;
    }
}
