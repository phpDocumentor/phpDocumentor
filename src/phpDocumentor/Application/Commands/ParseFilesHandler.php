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
use phpDocumentor\Partials\Collection;

final class ParseFilesHandler
{
    /** @var Parser */
    private $parser;

    /** @var Configuration */
    private $configuration;

    /** @var Collection */
    private $partialCollection;

    public function __construct(Parser $parser, Configuration $configuration, Collection $partialCollection)
    {
        $this->parser = $parser;
        $this->configuration = $configuration;
        $this->partialCollection = $partialCollection;
    }

    public function __invoke(ParseFiles $parseFiles)
    {
        $projectDescriptor = $this->parser->parse();
        $projectDescriptor->setName($this->configuration->getTitle());
        $projectDescriptor->setPartials($this->partialCollection);
    }
}
