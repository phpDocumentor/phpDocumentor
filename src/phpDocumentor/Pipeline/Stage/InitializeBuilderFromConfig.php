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

namespace phpDocumentor\Pipeline\Stage;

use OutOfRangeException;
use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Collection as PartialsCollection;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Guides\Nodes\ProjectNode;

use function count;
use function md5;

final class InitializeBuilderFromConfig
{
    /** @param PartialsCollection<string> $partials */
    public function __construct(private readonly PartialsCollection $partials)
    {
    }

    public function __invoke(Payload $payload): Payload
    {
        $configuration = $payload->getConfig();

        $builder = $payload->getBuilder();
        $builder->createProjectDescriptor();
        $builder->setName($configuration['phpdocumentor']['title'] ?? '');
        $builder->setPartials($this->partials);
        $builder->setCustomSettings($configuration['phpdocumentor']['settings']);

        foreach ($configuration['phpdocumentor']['versions'] as $version) {
            $builder->addVersion($this->buildVersion($version));
        }

        return $payload;
    }

    private function buildVersion(VersionSpecification $version): VersionDescriptor
    {
        $collection = Collection::fromClassString(DocumentationSetDescriptor::class);

        $this->guardAgainstMultipleSetsOfTheSameType($version);

        foreach ($version->getGuides() as $guide) {
            $collection->add(
                new GuideSetDescriptor(
                    md5((string) $guide['output']),
                    $guide['source'],
                    $guide['output'],
                    $guide['format'],
                    projectNode: new ProjectNode(null, $version->getNumber()),
                ),
            );
        }

        foreach ($version->getApi() as $apiSpecification) {
            $collection->add(
                new ApiSetDescriptor(
                    md5((string) $apiSpecification['output']),
                    $apiSpecification['source'],
                    $apiSpecification['output'],
                    $apiSpecification,
                ),
            );
        }

        return new VersionDescriptor(
            $version->getNumber(),
            $collection,
        );
    }

    /**
     * Until official support is added for versions, check whether there is 1 and fail when multiple is
     * given.
     *
     * By adding this restriction, it should prevent confusion with users when they were to add multiple
     * in the configuration.
     */
    private function guardAgainstMultipleSetsOfTheSameType(VersionSpecification $version): void
    {
        if (count((array) $version->getGuides()) > 1) {
            throw new OutOfRangeException(
                <<<'EOF'
phpDocumentor supports 1 set of guides at the moment, support for multiple 
sets is being worked on.

If you see this message, it may be that you have defined multiple "<guide>"
elements in your configuration file.

To fix this, make sure you only have one "<guide>" element in your
documentation.
EOF,
            );
        }

        if (count($version->getApi()) > 1) {
            throw new OutOfRangeException(
                <<<'EOF'
phpDocumentor supports 1 set of API documentation at the moment, support for 
multiple sets is being worked on.

If you see this message, it may be that you have defined multiple "<api>" 
elements in your configuration file, or because you have used absolute paths
as value for the "-d" or "--directory" argument. phpDocumentor interprets 
absolute paths as separate components, each with their own set of 
documentation.

To fix this, make sure you only have one "<api>" element in your documentation
and/or use relative paths when using the "-d" or "--directory" command line
argument.
EOF,
            );
        }
    }
}
