<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use InvalidArgumentException;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\References\ResolvedReference;
use Psr\Log\LoggerInterface;

use function in_array;
use function sprintf;

final class ReferenceRegistry
{
    /** @var Reference[] */
    private $references = [];

    /** @var string[] */
    private $dependencies = [];

    /** @var string[] */
    private $unresolvedDependencies = [];

    /** @var string[] */
    private $originalDependencyNames = [];

    /** @var InvalidLink[] */
    private $invalidLinks = [];

    /** @var LoggerInterface */
    private $logger;

    /** @var UrlGenerator */
    private $urlGenerator;

    public function __construct(LoggerInterface $logger, UrlGenerator $urlGenerator)
    {
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
    }

    public function registerReference(Reference $reference): void
    {
        $this->references[$reference->getRole()] = $reference;
    }

    public function resolve(
        Environment $environment,
        string      $section,
        string      $data,
        ?Entry      $metaEntry
    ): ?ResolvedReference {
        if (!isset($this->references[$section])) {
            $this->addMissingReferenceSectionError($environment->getCurrentFileName(), $section);

            return null;
        }

        $reference = $this->references[$section];

        $resolvedReference = $reference->resolve($environment, $data);

        if ($resolvedReference === null) {
            $this->addInvalidLink(new InvalidLink($data));

            if ($metaEntry !== null) {
                $metaEntry->removeDependency(
                    // use the original name
                    $this->originalDependencyNames[$data] ?? $data
                );
            }

            return null;
        }

        if (isset($this->unresolvedDependencies[$data]) && $metaEntry !== null) {
            $metaEntry->resolveDependency(
                // use the unique, unresolved name
                $this->unresolvedDependencies[$data],
                $resolvedReference->getFile()
            );
        }

        return $resolvedReference;
    }

    public function addDependency(string $dependency, bool $requiresResolving = false): void
    {
        if ($requiresResolving) { // a hack to avoid collisions between resolved and unresolved dependencies
            $dependencyName = 'UNRESOLVED__' . $dependency;
            $this->unresolvedDependencies[$dependency] = $dependencyName; // map the original dependency name to the one that will be stored
            $this->originalDependencyNames[$dependency] = $dependencyName;
        } else { // the dependency is already a filename, probably a :doc:
            // or from a toc-tree - change it to the canonical URL
            $canonicalDependency = $this->urlGenerator->canonicalUrl('', $dependency);
            if ($canonicalDependency === null) {
                throw new InvalidArgumentException(
                    sprintf('Could not get canonical url for dependency %s', $dependency)
                );
            }
            $dependencyName = $canonicalDependency; // map the original dependency name to the one that will be stored
            $this->originalDependencyNames[$dependency] = $canonicalDependency;
        }
        if (in_array($dependencyName, $this->dependencies, true)) {
            return;
        }
        $this->dependencies[] = $dependencyName;
    }

    public function addInvalidLink(InvalidLink $invalidLink): void
    {
        $this->invalidLinks[] = $invalidLink;
    }

    /**
     * @return InvalidLink[]
     */
    public function getInvalidLinks(): array
    {
        return $this->invalidLinks;
    }

    /**
     * @param array{anchor: ?string, domain: string, section: string, text: ?string, url: ?string} $data
     *
     * @return string[]|null
     */
    public function found(Environment $environment, string $section, array $data): ?array
    {
        $role = $section;
        if ($data['domain'] ?? '') {
            $role = $data['domain'] . ':' . $role;
        }

        if (isset($this->references[$role])) {
            $reference = $this->references[$role];

            $reference->found($this, $data['url']);

            return null;
        }

        $this->addMissingReferenceSectionError($environment->getCurrentFileName(), $role);

        return null;
    }

    private function addMissingReferenceSectionError(string $currentFileName, string $section): void
    {
        $this->addError(
            sprintf(
                'Unknown reference section "%s"%s',
                $section,
                $currentFileName !== '' ? sprintf(' in "%s" ', $currentFileName) : ''
            )
        );
    }

    public function addError(string $message): void
    {
        $this->logger->error($message);
    }

    /**
     * @return string[]
     */
    public function getDependencies(): array
    {
        return $this->dependencies;
    }
}
