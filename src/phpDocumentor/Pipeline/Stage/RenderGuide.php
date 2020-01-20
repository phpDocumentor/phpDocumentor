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

namespace phpDocumentor\Pipeline\Stage;

use Doctrine\RST\Builder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use phpDocumentor\Dsn;
use phpDocumentor\Guides\BuildContext;
use phpDocumentor\Guides\Generator\HtmlForPdfGenerator;
use phpDocumentor\Guides\Generator\JsonGenerator;
use phpDocumentor\Guides\KernelFactory;
use phpDocumentor\Parser\FileCollector;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Parser\FlySystemMirror;
use phpDocumentor\Path;
use Psr\Log\LoggerInterface;

/**
 * @experimental Do not use; this stage is meant as a sandbox / playground to experiment with generating guides.
 */
final class RenderGuide
{
    /** @var LoggerInterface */
    private $logger;

    /** @var FileCollector */
    private $fileCollector;

    /** @var FlySystemFactory */
    private $flySystemFactory;
    /**
     * @var KernelFactory
     */
    private $kernelFactory;

    public function __construct(
        LoggerInterface $logger,
        FileCollector $fileCollector,
        FlySystemFactory $flySystemFactory,
        KernelFactory $kernelFactory
    ) {
        $this->logger = $logger;
        $this->fileCollector = $fileCollector;
        $this->flySystemFactory = $flySystemFactory;
        $this->kernelFactory = $kernelFactory;
    }

    public function __invoke(Payload $payload)
    {
        if (!($payload->getBuilder()->getProjectDescriptor()->getSettings()->getCustom()['guides.enabled'] ?? false)) {
            return $payload;
        }

        $configuration = $payload->getConfig();

        /** @var Dsn $dsn */
        $dsn = $configuration['phpdocumentor']['paths']['output'];
        $dsn = $dsn->withPath(new Path(((string) $dsn->getPath()) . '/docs'));
        $output = $this->flySystemFactory->create($dsn);

        foreach ($configuration['phpdocumentor']['versions'] as $version) {
            foreach ($version['guides'] ?? [] as $guide) {
                // TODO: Yes, hardcoded for the POC; because we need to convert the builder to use flysystem or
                //       the filecollector
                $this->renderGuide(__DIR__ . '/../../../../docs', $output);
            }
        }

        // Temporary die on purpose; prevents API docs from processing for now. The feature flag above will ensure
        // normal operation
        die();
        return $payload;
    }

    private function renderGuide(string $source, FilesystemInterface $output) : array
    {
        $buildContext = new BuildContext(
            'https://api.symfony.com',
            'https://docs.symfony.com',
            'https://docs.symfony.com'
        );
        $buildContext->initializeRuntimeConfig($source, $output);

        $builder = new Builder($this->kernelFactory->createKernel($buildContext));

        $temporaryFolder = sys_get_temp_dir() . '/phpdocumentor/guide/output';
        $builder->build($buildContext->getSourceDir(), $temporaryFolder);

        $sourceFilesystem = new Filesystem(new Local($temporaryFolder));
        FlySystemMirror::mirror($sourceFilesystem, $buildContext->getOutputFilesystem());

        $metas = $builder->getMetas();
        if ($buildContext->getParseSubPath()) {
            $htmlForPdfGenerator = new HtmlForPdfGenerator($metas, $buildContext);
            $htmlForPdfGenerator->generateHtmlForPdf();
        } elseif (false) {
            $jsonGenerator = new JsonGenerator($metas, $buildContext);
            $jsonGenerator->setOutput($this->io);
            $jsonGenerator->generateJson();
        }

        return $builder->getErrorManager()->getErrors();
    }
}
