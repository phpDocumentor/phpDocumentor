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

namespace phpDocumentor\Guides\Twig;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Webmozart\Assert\Assert;

use function get_class;
use function sprintf;
use function trim;

final class AssetsExtension extends AbstractExtension
{
    /** @var LoggerInterface */
    private $logger;

    /** @var PlantumlRenderer */
    private $plantumlRenderer;

    public function __construct(LoggerInterface $logger, PlantumlRenderer $plantumlRenderer)
    {
        $this->logger = $logger;
        $this->plantumlRenderer = $plantumlRenderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', [$this, 'asset'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('renderNode', [$this, 'renderNode'], ['is_safe' => ['html'], 'needs_context' => true]),
            new TwigFunction('uml', [$this, 'uml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Copies the referenced asset and returns the canonical path to that asset; thus taking the BASE tag into account.
     *
     * The layout for guides includes a BASE tag in the head, which creates the need for all relative urls to actually
     * be relative not to the current file's path; but the root of the Documentation Set. This means that, when
     * rendering paths, you always need to include the canonical path; not that relative to the current file.
     *
     * @param mixed[] $context
     */
    public function asset(array $context, string $path): string
    {
        $outputPath = $this->copyAsset(
            $context['env'] ?? null,
            $context['destination'] ?? null,
            $path
        );

        // make it relative so it plays nice with the base tag in the HEAD
        return trim($outputPath, '/');
    }

    /**
     * @param mixed[] $context
     */
    public function renderNode(array $context, Node $node): string
    {
        $environment = $context['env'] ?? null;
        if (!$environment instanceof Environment) {
            throw new RuntimeException('Environment must be set in the twig global state to render nodes');
        }

        return $environment->getNodeRendererFactory()->get(get_class($node))->render($node);
    }

    public function uml(string $source): ?string
    {
        return $this->plantumlRenderer->render($source);
    }

    private function copyAsset(?Environment $environment, ?FilesystemInterface $destination, string $path): string
    {
        if (!$environment instanceof Environment) {
            return $path;
        }

        if (!$destination instanceof FilesystemInterface) {
            return $path;
        }

        $sourcePath = $environment->absoluteRelativePath($path);
        $outputPath = $environment->outputUrl($path);
        Assert::string($outputPath);
        if ($environment->getOrigin()->has($sourcePath) === false) {
            $this->logger->error(sprintf('Image reference not found "%s"', $sourcePath));

            return $outputPath;
        }

        $fileContents = $environment->getOrigin()->read($sourcePath);
        if ($fileContents === false) {
            $this->logger->error(sprintf('Could not read image file "%s"', $sourcePath));

            return $outputPath;
        }

        $destination->put($outputPath, $fileContents);

        return $outputPath;
    }
}
