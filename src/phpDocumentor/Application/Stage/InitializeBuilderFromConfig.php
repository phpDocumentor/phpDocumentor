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

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Application\Stage\Payload;
use phpDocumentor\Descriptor\Collection as PartialsCollection;

final class InitializeBuilderFromConfig
{
    private $partials;

    public function __construct(PartialsCollection $partials)
    {
        $this->partials = $partials;
    }

    public function __invoke(Payload $payload)
    {
        $configuration = $payload->getConfig();

        $builder = $payload->getBuilder();
        $builder->createProjectDescriptor();
        $builder->setName($configuration['phpdocumentor']['title']);
        $builder->setPartials($this->partials);

        return $payload;
    }
}
