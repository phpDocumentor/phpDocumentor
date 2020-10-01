<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Meta;

use phpDocumentor\Guides\RestructuredText\Environment;
use function strtolower;

class Metas
{
    /** @var MetaEntry[] */
    private $entries = [];

    /** @var string[] */
    private $parents = [];

    /**
     * @param MetaEntry[] $entries
     */
    public function __construct(array $entries = [])
    {
        $this->entries = $entries;
    }

    public function findLinkMetaEntry(string $link) : ?MetaEntry
    {
        foreach ($this->entries as $entry) {
            if ($this->doesLinkExist($entry->getLinks(), $link)) {
                return $entry;
            }
        }

        return $this->findByTitle($link);
    }

    /**
     * @return MetaEntry[]
     */
    public function getAll() : array
    {
        return $this->entries;
    }

    /**
     * @param string[][] $titles
     * @param mixed[][]  $tocs
     * @param string[]   $depends
     * @param string[]   $links
     */
    public function set(
        string $file,
        string $url,
        string $title,
        array $titles,
        array $tocs,
        int $mtime,
        array $depends,
        array $links
    ) : void {
        foreach ($tocs as $toc) {
            foreach ($toc as $child) {
                $this->parents[$child] = $file;

                if (! isset($this->entries[$child])) {
                    continue;
                }

                $this->entries[$child]->setParent($file);
            }
        }

        $this->entries[$file] = new MetaEntry(
            $file,
            $url,
            $title,
            $titles,
            $tocs,
            $depends,
            $links,
            $mtime
        );

        if (! isset($this->parents[$file])) {
            return;
        }

        $this->entries[$file]->setParent($this->parents[$file]);
    }

    public function get(string $url) : ?MetaEntry
    {
        if (isset($this->entries[$url])) {
            return $this->entries[$url];
        }

        return null;
    }

    /**
     * @param MetaEntry[] $metaEntries
     */
    public function setMetaEntries(array $metaEntries) : void
    {
        $this->entries = $metaEntries;
    }

    /**
     * @param string[] $links
     */
    private function doesLinkExist(array $links, string $link) : bool
    {
        foreach ($links as $name => $url) {
            if ($name === strtolower($link)) {
                return true;
            }
        }

        return false;
    }

    private function findByTitle(string $text) : ?MetaEntry
    {
        $text = Environment::slugify($text);

        // try to lookup the document reference by title
        foreach ($this->entries as $entry) {
            if ($entry->hasTitle($text)) {
                return $entry;
            }
        }

        return null;
    }
}
