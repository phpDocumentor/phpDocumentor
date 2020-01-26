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

namespace phpDocumentor\Transformer\Writer;

use Doctrine\RST\Builder;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Guides\BuildContext;
use phpDocumentor\Guides\KernelFactory;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Parser\FlySystemMirror;
use phpDocumentor\Transformer\Transformation;
use function rtrim;
use function sprintf;

/**
 * @experimental Do not use; this stage is meant as a sandbox / playground to experiment with generating guides.
 */
final class RenderGuide extends WriterAbstract
{
    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var KernelFactory */
    private $kernelFactory;

    /** @var string */
    private $globalCachePath;

    public function __construct(
        FlySystemFactory $flySystemFactory,
        KernelFactory $kernelFactory,
        string $globalCachePath
    ) {
        $this->flySystemFactory = $flySystemFactory;
        $this->kernelFactory = $kernelFactory;
        $this->globalCachePath = $globalCachePath;
    }

    /**
     * @inheritDoc
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation) : void
    {
        // Feature flag: Guides are disables by default since this is an experimental feature
        if (!($project->getSettings()->getCustom()['guides.enabled'] ?? false)) {
            return;
        }

        $output = $transformation->getTransformer()->destination();
        $cachePath = sprintf('%s/guide', $this->globalCachePath);

        /** @var VersionDescriptor $version */
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                if (!$documentationSet instanceof GuideSetDescriptor) {
                    continue;
                }

                $buildContext = new BuildContext(
                    $output,
                    $documentationSet->getOutput(),
                    'default',
                    $cachePath,
                    $project->getSettings()->getCustom()['guides.cache'] ?? true
                );

                $inputFolder = rtrim(
                    $this->determineInputFolder($documentationSet->getSource()['dsn'], $cachePath),
                    '/'
                );
                $inputFolder .= $documentationSet->getSource()['paths'][0] ?? '';

                $tempOutputPath = sprintf('%s/output', $cachePath);

                $builder = new Builder($this->kernelFactory->createKernel($project, $buildContext));
                $builder->build($inputFolder, $tempOutputPath);

                $tempFilesystem = new Filesystem(new Local($tempOutputPath));
                FlySystemMirror::mirror($tempFilesystem, $output, '', $buildContext->getDestinationPath());
                // $builder->getErrorManager()->getErrors();
            }
        }
    }

    private function determineInputFolder(Dsn $dsn, string $cachePath) : string
    {
        if ($dsn->getScheme() === null) {
            return (string) $dsn->getPath();
        }

        $input = $this->flySystemFactory->create($dsn);
        $inputFolder = sprintf('%s/input', $cachePath);

        $inputFilesystem = new Filesystem(new Local($inputFolder));
        FlySystemMirror::mirror($input, $inputFilesystem);

        return $inputFolder;
    }
}
