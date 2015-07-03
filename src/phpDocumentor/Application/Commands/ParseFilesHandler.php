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

use phpDocumentor\Configuration;
use phpDocumentor\Parser\Parser;

final class ParseFilesHandler
{
    /** @var Parser */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function __invoke(ParseFiles $command)
    {
        $projectDescriptor = $this->parser->parse();
        $projectDescriptor->setName($command->getConfiguration()->getTitle());
    }
}
