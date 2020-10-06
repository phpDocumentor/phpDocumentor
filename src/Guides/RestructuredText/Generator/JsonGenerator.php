<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\RestructuredText\Generator;

use Exception;
use phpDocumentor\Guides\BuildContext;
use phpDocumentor\Guides\Meta\Entry;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Environment;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use function count;

class JsonGenerator
{
    private $metas;

    private $buildContext;

    /** @var SymfonyStyle|null */
    private $output;

    public function __construct(Metas $metas, BuildContext $buildContext)
    {
        $this->metas = $metas;
        $this->buildContext = $buildContext;
    }

    public function generateJson() : void
    {
        $fs = new Filesystem();

        $progressBar = new ProgressBar($this->output ?: new NullOutput());
        $progressBar->setMaxSteps(count($this->metas->getAll()));

        foreach ($this->metas->getAll() as $filename => $metaEntry) {
            $parserFilename = $filename;
            $jsonFilename = $this->buildContext->getOutputFilesystem() . '/' . $filename . '.fjson';

            $crawler = new Crawler(
                file_get_contents($this->buildContext->getOutputFilesystem() . '/' . $filename . '.html')
            );

            $data = [
                'title' => $metaEntry->getTitle(),
                'current_page_name' => $parserFilename,
                'toc' => $this->generateToc($metaEntry, current($metaEntry->getTitles())[1]),
                'next' => $this->guessNext($parserFilename),
                'prev' => $this->guessPrev($parserFilename),
                'rellinks' => [
                    $this->guessNext($parserFilename),
                    $this->guessPrev($parserFilename),
                ],
                'body' => $crawler->filter('body')->html(),
            ];

            $fs->dumpFile(
                $jsonFilename,
                json_encode($data, JSON_PRETTY_PRINT)
            );

            $progressBar->advance();
        }

        $progressBar->finish();
    }

    public function setOutput(SymfonyStyle $output) : void
    {
        $this->output = $output;
    }

    private function generateToc(Entry $metaEntry, ?array $titles) : array
    {
        if (null === $titles) {
            return [];
        }

        $tocTree = [];

        foreach ($titles as $title) {
            $tocTree[] = [
                'url' => sprintf('%s#%s', $metaEntry->getUrl(), Environment::slugify($title[0])),
                'title' => $title[0],
                'children' => $this->generateToc($metaEntry, $title[1]),
            ];
        }

        return $tocTree;
    }

    private function guessNext(string $parserFilename) : ?array
    {
        $meta = $this->getMetaEntry($parserFilename, true);

        $parentFile = $meta->getParent();

        // if current file is an index, next is the first chapter
        if ('index' === $parentFile && 1 === count($tocs = $meta->getTocs()) && count($tocs[0]) > 0) {
            $firstChapterMeta = $this->getMetaEntry($tocs[0][0]);

            if (null === $firstChapterMeta) {
                return null;
            }

            return [
                'title' => $firstChapterMeta->getTitle(),
                'link' => $firstChapterMeta->getUrl(),
            ];
        }

        [$toc, $indexCurrentFile] = $this->getNextPrevInformation($parserFilename);

        if (!isset($toc[$indexCurrentFile + 1])) {
            return null;
        }

        $nextFileName = $toc[$indexCurrentFile + 1];

        $nextMeta = $this->getMetaEntry($nextFileName);

        if (null === $nextMeta) {
            return null;
        }

        return [
            'title' => $nextMeta->getTitle(),
            'link' => $nextMeta->getUrl(),
        ];
    }

    private function guessPrev(string $parserFilename) : ?array
    {
        $meta = $this->getMetaEntry($parserFilename, true);
        $parentFile = $meta->getParent();

        // no prev if parent is an index
        if ('index' === $parentFile) {
            return null;
        }

        [$toc, $indexCurrentFile] = $this->getNextPrevInformation($parserFilename);

        // if current file is the first one of the chapter, prev is the direct parent
        if (0 === $indexCurrentFile) {
            $parentMeta = $this->getMetaEntry($parentFile);

            if (null === $parentMeta) {
                return null;
            }

            return [
                'title' => $parentMeta->getTitle(),
                'link' => $parentMeta->getUrl(),
            ];
        }

        if (!isset($toc[$indexCurrentFile - 1])) {
            return null;
        }

        $prevFileName = $toc[$indexCurrentFile - 1];

        $prevMeta = $this->getMetaEntry($prevFileName);

        if (null === $prevMeta) {
            return null;
        }

        return [
            'title' => $prevMeta->getTitle(),
            'link' => $prevMeta->getUrl(),
        ];
    }

    private function getNextPrevInformation(string $parserFilename) : array
    {
        $meta = $this->getMetaEntry($parserFilename, true);
        $parentFile = $meta->getParent();

        if (!$parentFile) {
            return [null, null];
        }

        $metaParent = $this->getMetaEntry($parentFile);

        if (null === $metaParent || !$metaParent->getTocs() || 1 !== count($metaParent->getTocs())) {
            return [null, null];
        }

        $toc = current($metaParent->getTocs());

        if (count($toc) < 2 || !isset(array_flip($toc)[$parserFilename])) {
            return [null, null];
        }

        $indexCurrentFile = array_flip($toc)[$parserFilename];

        return [$toc, $indexCurrentFile];
    }

    private function getMetaEntry(string $parserFilename, bool $throwOnMissing = false) : ?Entry
    {
        $metaEntry = $this->metas->get($parserFilename);

        // this is possible if there are invalid references
        if (null === $metaEntry) {
            $message = sprintf('Could not find MetaEntry for file "%s"', $parserFilename);

            if ($throwOnMissing) {
                throw new Exception($message);
            }

            if ($this->output) {
                $this->output->note($message);
            }
        }

        return $metaEntry;
    }
}
