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

namespace phpDocumentor\Compiler\Guides\Pass;

use phpDocumentor\Compiler\DescriptorRepository;
use phpDocumentor\Descriptor\DocumentDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Guides\Compiler\Compiler;
use phpDocumentor\Guides\Compiler\CompilerPass;
use phpDocumentor\Guides\Compiler\DescriptorAwareCompilerContext;
use phpDocumentor\Guides\Compiler\NodeTransformers\NodeTransformerFactory;
use phpDocumentor\Guides\Nodes\DocumentNode;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/** @coversDefaultClass \phpDocumentor\Compiler\Guides\Pass\GuidesCompiler */
final class GuidesCompilerTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    private GuidesCompiler $guidesCompiler;
    private Compiler $compiler;
    private DescriptorRepository $descriptorRepository;

    private ObjectProphecy $testPass;

    protected function setUp(): void
    {
        $this->testPass = $this->prophesize(CompilerPass::class);
        $this->testPass->getPriority()->willReturn(1000);
        $factory = $this->prophesize(NodeTransformerFactory::class);
        $factory->getTransformers()->willReturn([]);
        $factory->getPriorities()->willReturn([1000]);
        $this->compiler = new Compiler([$this->testPass->reveal()], $factory->reveal());
        $this->descriptorRepository = new DescriptorRepository();

        $this->guidesCompiler = new GuidesCompiler(
            $this->compiler,
            $this->descriptorRepository,
        );
    }

    public function testGetDescription(): void
    {
        $description = $this->guidesCompiler->getDescription();

        $this->assertSame('Compiling guides', $description);
    }

    public function testInvokeReturnsUnmodifiedWhenSubjectIsNotAGuideSetDescriptor(): void
    {
        $apiSetDescriptor = self::faker()->apiSetDescriptor();

        $result = ($this->guidesCompiler)($apiSetDescriptor);

        $this->assertSame($apiSetDescriptor, $result);
    }

    public function testCompilerReplacesDocumentNodesWithNewlyCompiledOnes(): void
    {
        $guideSetDescriptor = self::faker()->guideSetDescriptor();
        $this->descriptorRepository->setVersionDescriptor(self::faker()->versionDescriptor([$guideSetDescriptor]));

        $originalDocumentNodes = $this->givenASeriesOfDocumentNodes($guideSetDescriptor);
        $compiledDocumentNodes = $this->expectingASeriesOfCompiledDocumentNodes($originalDocumentNodes);
        $this->expectingGuidesCompilerToReturnCompiledDocumentNodes($originalDocumentNodes, $compiledDocumentNodes);

        /** @var GuideSetDescriptor $result */
        $result = $this->guidesCompiler->__invoke($guideSetDescriptor);

        self::assertSame($guideSetDescriptor, $result);

        foreach ($result->getDocuments() as $documentDescriptor) {
            self::assertSame(
                $compiledDocumentNodes[$documentDescriptor->getFile()],
                $documentDescriptor->getDocumentNode(),
            );
        }
    }

    /** @return array<string, DocumentNode> */
    private function givenASeriesOfDocumentNodes(GuideSetDescriptor $guideSetDescriptor): array
    {
        $originalDocumentNodes = [];
        for ($i = 1; $i <= 3; $i++) {
            $md5 = self::faker()->md5();
            $filePath = self::faker()->filePath();
            $documentNode = new DocumentNode($md5, $filePath);
            $document = new DocumentDescriptor($documentNode, $md5, $filePath, self::faker()->word());

            $originalDocumentNodes[$filePath] = $documentNode;
            $guideSetDescriptor->getDocuments()->set($filePath, $document);
        }

        return $originalDocumentNodes;
    }

    /**
     * @param array<string, DocumentNode> $originalDocumentNodes
     *
     * @return array<string, DocumentNode>
     */
    private function expectingASeriesOfCompiledDocumentNodes(array $originalDocumentNodes): array
    {
        $compiledDocumentNodes = [];
        foreach ($originalDocumentNodes as $documentNode) {
            $md5 = self::faker()->md5();
            $compiledDocumentNode = new DocumentNode($md5, $documentNode->getFilePath());

            $compiledDocumentNodes[$compiledDocumentNode->getFilePath()] = $compiledDocumentNode;
        }

        return $compiledDocumentNodes;
    }

    /**
     * @param array<string, DocumentNode> $originalDocumentNodes
     * @param array<string, DocumentNode> $compiledDocumentNodes
     */
    private function expectingGuidesCompilerToReturnCompiledDocumentNodes(
        array $originalDocumentNodes,
        array $compiledDocumentNodes,
    ): void {
        $this->testPass->run($originalDocumentNodes, Argument::type(DescriptorAwareCompilerContext::class))
            ->willReturn($compiledDocumentNodes);
    }
}
