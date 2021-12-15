<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use InvalidArgumentException;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Nodes\Links\InvalidLink;
use phpDocumentor\Guides\References\Reference;
use phpDocumentor\Guides\References\ResolvedReference;
use Psr\Log\LoggerInterface;

use function sprintf;

/**
 * Constructs a listing of dependent documents for the given scope.
 *
 * This builder is used to collect the document identifiers of dependencies found through references. For example, the
 * `:doc:` reference role will refer to a document id (i.e. `getting-started/configuration`, note the absence of a file
 * extension) and by passing it through this builder we resolve and register dependencies directly on the dependent
 * Document, or Scope.
 *
 * To use this class, first call {@see self::scope()} to register for which document to resolve dependencies.
 */
class ReferenceBuilder
{
    /** @var Reference[] */
    private $typesOfReferences = [];

    /** @var LoggerInterface */
    private $logger;

    /** @var UrlGenerator */
    private $urlGenerator;

    /** @var Nodes\DocumentNode|null */
    private $scope;

    /** @var string[] */
    private $unresolvedDependencies = [];

    /** @var string[] */
    private $originalDependencyNames = [];

    /** @var InvalidLink[] */
    private $invalidLinks = [];

    public function __construct(LoggerInterface $logger, UrlGenerator $urlGenerator)
    {
        $this->logger = $logger;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Register a type of reference to support, such as `:doc:`.
     */
    public function registerTypeOfReference(Reference $typeOfReference): void
    {
        $this->typesOfReferences[$typeOfReference->getRole()] = $typeOfReference;
    }

    /**
     * Declare which document node to resolve dependencies for.
     */
    public function scope(Nodes\DocumentNode $document): void
    {
        $this->scope = $document;
        $this->unresolvedDependencies = [];
        $this->originalDependencyNames = [];
        $this->invalidLinks = [];
    }

    public function resolve(
        Environment $environment,
        string $role,
        string $data,
        ?Entry $metaEntry = null
    ): ?ResolvedReference {
        if (!isset($this->typesOfReferences[$role])) {
            // TODO: The current file name, should this not be registered on the DocumentNode?
            $this->addMissingReferenceSectionError($environment->getCurrentFileName(), $role);

            return null;
        }

        $reference = $this->typesOfReferences[$role];

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
        if ($requiresResolving) {
            // a hack to avoid collisions between resolved and unresolved dependencies
            $dependencyName = 'UNRESOLVED__' . $dependency;

            // map the original dependency name to the one that will be stored
            $this->unresolvedDependencies[$dependency] = $dependencyName;
            $this->originalDependencyNames[$dependency] = $dependencyName;
        } else {
            // the dependency is already a filename, probably a :doc:
            // or from a toc-tree - change it to the canonical URL
            $canonicalDependency = $this->urlGenerator->canonicalUrl('', $dependency);
            if ($canonicalDependency === '') {
                throw new InvalidArgumentException(
                    sprintf('Could not get canonical url for dependency %s', $dependency)
                );
            }

            $dependencyName = $canonicalDependency; // map the original dependency name to the one that will be stored
            $this->originalDependencyNames[$dependency] = $canonicalDependency;
        }

        $this->scope->addDependency($dependencyName);
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
    public function found(string $currentFileName, string $section, array $data): ?array
    {
        $role = $section;
        if ($data['domain'] ?? '') {
            $role = $data['domain'] . ':' . $role;
        }

        if (isset($this->typesOfReferences[$role])) {
            $reference = $this->typesOfReferences[$role];

            $reference->found($this, $data['url']);

            return null;
        }

        $this->addMissingReferenceSectionError($currentFileName, $role);

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
}
