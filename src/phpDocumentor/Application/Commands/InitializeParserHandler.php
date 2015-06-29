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
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Parser\Parser;

final class InitializeParserHandler
{
    /** @var InitializerChain */
    private $initializerChain;

    /** @var Parser */
    private $parser;

    /** @var Analyzer */
    private $analyzer;

    /** @var Finder */
    private $exampleFinder;

    public function __construct(
        InitializerChain $initializerChain,
        Parser $parser,
        Analyzer $analyzer,
        Finder $exampleFinder
    ) {
        $this->initializerChain = $initializerChain;
        $this->parser           = $parser;
        $this->analyzer         = $analyzer;
        $this->exampleFinder    = $exampleFinder;
    }

    public function __invoke(InitializeParser $command)
    {
        $this->initializerChain->initialize($this->analyzer);
        $this->parser->boot($command->getConfiguration()->getParser());
        $this->configureExampleFinder($command->getConfiguration());
    }

    /**
     * Configures the paths of the example finder to match the configuration.
     *
     * @param Configuration $configuration
     *
     * @return void
     */
    private function configureExampleFinder(Configuration $configuration)
    {
        $this->exampleFinder->setSourceDirectory($this->parser->getFiles()->getProjectRoot());
        $this->exampleFinder->setExampleDirectories($configuration->getFiles()->getExamples());
    }
}
