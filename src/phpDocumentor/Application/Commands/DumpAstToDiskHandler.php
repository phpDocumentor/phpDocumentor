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

/**
 * Dumps a serialized version of the project to a provided location.
 */
final class DumpAstToDiskHandler
{
    /** @var Analyzer */
    private $analyzer;

    /**
     * Registers the required dependencies on this handler.
     *
     * @param Analyzer $analyzer
     */
    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * Dumps the project to the location provided in the command.
     *
     * @param DumpAstToDisk $command
     *
     * @return void
     */
    public function __invoke(DumpAstToDisk $command)
    {
        file_put_contents($command->getLocation(), serialize($this->analyzer->getProjectDescriptor()));
    }
}
