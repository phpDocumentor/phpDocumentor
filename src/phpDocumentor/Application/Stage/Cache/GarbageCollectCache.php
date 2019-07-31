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

namespace phpDocumentor\Application\Stage\Cache;

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Application\Stage\Parser\Payload;

final class GarbageCollectCache
{
    /**
     * @var ProjectDescriptorMapper
     */
    private $descriptorMapper;

    public function __construct(ProjectDescriptorMapper $descriptorMapper)
    {
        $this->descriptorMapper = $descriptorMapper;
    }

    public function __invoke(Payload $payload)
    {
        $this->descriptorMapper->garbageCollect($payload->getFiles());
        return $payload;
    }
}
