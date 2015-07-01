<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Descriptor\Analyzer;

final class DumpAstToDiskHandler
{
    /**
     * @var Analyzer
     */
    private $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function __invoke(DumpAstToDisk $command)
    {
        file_put_contents($command->getLocation(), serialize($this->analyzer->getProjectDescriptor()));
    }
}
