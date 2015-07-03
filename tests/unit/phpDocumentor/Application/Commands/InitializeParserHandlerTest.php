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

use Mockery as m;
use phpDocumentor\Configuration;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Parser\Parser;

/**
 * @coversDefaultClass phpDocumentor\Application\Commands\InitializeParserHandler
 */
class InitializeParserHandlerTest extends \PHPUnit_Framework_TestCase
{
    /** @var InitializerChain|m\MockInterface */
    private $initializerChain;

    /** @var Parser|m\MockInterface */
    private $parser;

    /** @var Finder|m\MockInterface */
    private $exampleFinder;

    /** @var Analyzer|m\MockInterface */
    private $analyzer;

    /** @var InitializeParserHandler */
    private $fixture;

    public function setUp()
    {
        $this->analyzer         = m::mock(Analyzer::class);
        $this->initializerChain = new InitializerChain();
        $this->parser           = m::mock(Parser::class);
        $this->exampleFinder    = m::mock(Finder::class);

        $this->fixture = new InitializeParserHandler(
            $this->initializerChain,
            $this->parser,
            $this->analyzer,
            $this->exampleFinder
        );
    }

    /**
     * @covers ::__construct
     * @covers ::__invoke
     * @uses phpDocumentor\Application\Commands\InitializeParser
     * @uses phpDocumentor\Descriptor\ProjectDescriptor
     */
    public function testParserIsInitialized()
    {
        $parserConfiguration = new \phpDocumentor\Parser\Configuration();
        $projectRoot         = 'projectRoot';
        $exampleFolders      = ['exampleFolder'];

        $configuration = m::mock(Configuration::class);
        $configuration->shouldReceive('getParser')->andReturn($parserConfiguration);
        $configuration->shouldReceive('getFiles->getExamples')->andReturn($exampleFolders);

        $command = new InitializeParser($configuration);

        $initializerIsCalled = false;
        $this->initializerChain->addInitializer(
            function () use (&$initializerIsCalled) {
                $initializerIsCalled = true;
            }
        );

        $this->parser->shouldReceive('boot')->with($parserConfiguration);
        $this->parser->shouldReceive('getFiles->getProjectRoot')->andReturn($projectRoot);

        $this->exampleFinder->shouldReceive('setSourceDirectory')->with($projectRoot);
        $this->exampleFinder->shouldReceive('setExampleDirectories')->with($exampleFolders);

        $this->fixture->__invoke($command);

        $this->assertTrue($initializerIsCalled);
    }
}
