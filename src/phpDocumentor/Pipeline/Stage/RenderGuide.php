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
use Doctrine\RST\Meta\Metas;
use phpDocumentor\Dsn;
use phpDocumentor\Guides\BuildContext;
use phpDocumentor\Guides\Generator\HtmlForPdfGenerator;
use phpDocumentor\Guides\Generator\JsonGenerator;
use phpDocumentor\Guides\KernelFactory;
use phpDocumentor\Parser\FileCollector;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @experimental Do not use; this stage is meant as a sandbox / playground to experiment with generating guides.
 */
final class RenderGuide
{
    private $logger;
    private $fileCollector;

    public function __construct(LoggerInterface $logger, FileCollector $fileCollector)
    {
        $this->logger = $logger;
        $this->fileCollector = $fileCollector;
    }

    public function __invoke(Payload $payload)
    {
        if (!($payload->getBuilder()->getProjectDescriptor()->getSettings()->getCustom()['guides.enabled'] ?? false)) {
            return $payload;
        }

        var_dump('test');
        $configuration = $payload->getConfig();
        $output = $this->getTargetLocationBasedOnDsn($configuration['phpdocumentor']['paths']['output']);

        if (!is_dir($output)) {
            mkdir($output, 0777, true);
        }
        $generateJson = false;
        $buildContext = new BuildContext(
            '1.0.0',
            'https://api.symfony.com',
            'https://docs.symfony.com',
            'https://docs.symfony.com'
        );
        $buildContext->initializeRuntimeConfig(
        // TODO: Yes, hardcoded for the POC; because we need to convert the builder to use flysystem or
        //       the filecollector
            __DIR__ . '/../../../../docs',
            $output
        );

        $builder = new Builder(
            KernelFactory::createKernel($buildContext, $this->urlChecker ?? null)
        );

        $builder->build(
            $buildContext->getSourceDir(),
            $buildContext->getOutputDir()
        );

        // contains the errors accumulated during the build
        //$buildErrors = $builder->getErrorManager()->getErrors();

        $metas = $builder->getMetas();
        if ($buildContext->getParseSubPath()) {
            $this->renderDocForPDF($metas, $buildContext);
        } elseif ($generateJson) {
            $this->generateJson($metas, $buildContext);
        }

        return $payload;
    }

    private function getTargetLocationBasedOnDsn(Dsn $dsn) : string
    {
        $target = $dsn->getPath();
        $fileSystem = new Filesystem();
        if (!$fileSystem->isAbsolutePath((string) $target)) {
            $target = getcwd() . DIRECTORY_SEPARATOR . $target;
        }

        // TODO: /docs is temporary; we should be able to reconfigure the subfolder for the guide
        return (string) $target . '/docs';
    }

    private function generateJson(Metas $metas, BuildContext $buildContext)
    {
        $jsonGenerator = new JsonGenerator($metas, $buildContext);
        $jsonGenerator->setOutput($this->io);
        $jsonGenerator->generateJson();
    }

    private function renderDocForPDF(Metas $metas, BuildContext $buildContext)
    {
        $htmlForPdfGenerator = new HtmlForPdfGenerator($metas, $buildContext);
        $htmlForPdfGenerator->generateHtmlForPdf();
    }
}
