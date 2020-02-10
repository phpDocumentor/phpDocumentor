<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Generator;

use Doctrine\RST\Meta\MetaEntry;
use Doctrine\RST\Meta\Metas;
use InvalidArgumentException;
use LogicException;
use phpDocumentor\Guides\BuildContext;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use function dirname;

class HtmlForPdfGenerator
{
    private $metas;

    private $buildContext;

    public function __construct(Metas $metas, BuildContext $buildContext)
    {
        $this->metas = $metas;
        $this->buildContext = $buildContext;
    }

    public function generateHtmlForPdf()
    {
        $finder = new Finder();
        $finder->in($this->buildContext->getOutputFilesystem())
            ->depth(0)
            ->notName([$this->buildContext->getParseSubPath(), '_images']);

        $fs = new Filesystem();
        foreach ($finder as $file) {
            $fs->remove($file->getRealPath());
        }

        $basePath = sprintf(
            '%s/%s',
            $this->buildContext->getOutputFilesystem(),
            $this->buildContext->getParseSubPath()
        );
        $indexFile = sprintf('%s/%s', $basePath, 'index.html');
        if (!$fs->exists($indexFile)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $indexFile));
        }

        // extracting all files from index's TOC, in the right order
        $parserFilename = $this->getParserFilename($indexFile, $this->buildContext->getOutputFilesystem());
        $meta = $this->getMetaEntry($parserFilename);
        $files = current($meta->getTocs());
        array_unshift($files, sprintf('%s/index', $this->buildContext->getParseSubPath()));

        // building one big html file with all contents
        $content = '';
        $htmlDir = $this->buildContext->getOutputFilesystem();
        $relativeImagesPath = str_repeat('../', substr_count($this->buildContext->getParseSubPath(), '/'));
        foreach ($files as $file) {
            $meta = $this->getMetaEntry($file);

            $filename = sprintf('%s/%s.html', $htmlDir, $file);
            if (!$fs->exists($filename)) {
                throw new LogicException(sprintf('File "%s" does not exist', $filename));
            }

            // extract <body> content
            $crawler = new Crawler(file_get_contents($filename));
            $fileContent = $crawler->filter('body')->html();

            $dir = dirname($meta->getFile());
            $fileContent = $this->fixInternalUrls($fileContent, $dir);

            $fileContent = $this->fixInternalImages($fileContent, $relativeImagesPath);

            $uid = str_replace('/', '-', $meta->getFile());
            $fileContent = $this->fixUniqueIdsAndAnchors($fileContent, $uid);

            $content .= "\n";
            $content .= sprintf('<div id="%s">%s</div>', $uid, $fileContent);
        }

        $content = sprintf(
            '<html><head><title>%s</title></head><body>%s</body></html>',
            $this->buildContext->getParseSubPath(),
            $content
        );

        $content = $this->cleanupContent($content);

        $filename = sprintf('%s/%s.html', $htmlDir, $this->buildContext->getParseSubPath());
        file_put_contents($filename, $content);
        $fs->remove($basePath);
    }

    private function fixInternalUrls(string $fileContent, string $dir) : string
    {
        return preg_replace_callback(
            '/href="([^"]+?)"/',
            function ($matches) use ($dir) {
                if (strpos($matches[1], 'http') === 0 || strpos($matches[1], '#') === 0) {
                    return $matches[0];
                }

                $path = [];
                foreach (explode('/', $dir . '/' . str_replace(['.html', '#'], ['', '-'], $matches[1])) as $part) {
                    if ('..' === $part) {
                        array_pop($path);
                    } else {
                        $path[] = $part;
                    }
                }

                $path = implode('-', $path);

                return sprintf('href="#%s"', $path);
            },
            $fileContent
        );
    }

    private function fixInternalImages(string $fileContent, string $relativeImagesPath) : string
    {
        return $fileContent = preg_replace('{src="(?:\.\./)+([^"]+?)"}', "src=\"$relativeImagesPath$1\"", $fileContent);
    }

    private function fixUniqueIdsAndAnchors(string $fileContent, string $uid) : string
    {
        return preg_replace_callback(
            '/id="([^"]+)"/',
            function ($matches) use ($uid) {
                return sprintf('id="%s-%s"', $uid, $matches[1]);
            },
            $fileContent
        );
    }

    private function cleanupContent(string $content)
    {
        // remove internal anchors
        $content = preg_replace('#<a class="headerlink"([^>]+)>¶</a>#', '', $content);

        // convert links to footnote
        $content = preg_replace_callback(
            '#<a href="(.*?)" class="reference external"(?:[^>]*)>(.*?)</a>#',
            function ($matches) {
                if (0 === strpos($matches[2], 'http')) {
                    return sprintf('<em><a href="%s">%s</a></em>', $matches[2], $matches[2]);
                }

                return sprintf('<em>%s</em><span class="footnote"><code>%s</code></span>', $matches[2], $matches[1]);
            },
            $content
        );

        return $content;
    }

    private function getMetaEntry(string $parserFilename) : MetaEntry
    {
        $metaEntry = $this->metas->get($parserFilename);

        if (null === $metaEntry) {
            throw new LogicException(sprintf('Could not find MetaEntry for file "%s"', $parserFilename));
        }

        return $metaEntry;
    }

    private function getParserFilename(string $filePath, string $inputDir) : string
    {
        return $parserFilename = str_replace([$inputDir . '/', '.html'], ['', ''], $filePath);
    }
}
