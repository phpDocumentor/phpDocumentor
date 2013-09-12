<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

/**
 *
 */
class Tutorials
{
    const DEFAULT_PREFIX = 'var';

    /** @var string */
    protected $prefix;

    /** @var mixed */
    protected $parser = null;

    protected $files = array();

    protected $directories = array();

    protected $collection = null;

    public function __construct()
    {
        $this->prefix = self::DEFAULT_PREFIX;
    }

    public function parse()
    {
        $this->collection = new Tutorials\Collection;

        foreach($this->directories as $key => $file) {
            list($name,) = explode('.', $file->getFilename(), 2);
            $this->collection->set($this->prefix . strtolower($name), $this->parseMarkdown($file->getPathname()));
        }

        foreach($this->files as $file) {
            $info = pathinfo($file);
            $this->collection->set($this->prefix . strtolower($info['filename']), $this->parseMarkdown($file));
        }

        return $this->collection;
    }

    protected  function parseMarkdown($file)
    {
        return $this->parser->transformMarkdown(file_get_contents($file));
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function setParser($parser)
    {
        $this->parser = $parser;

        return $this;
    }

    public function addFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @param \DirectoryIterator|null $directory
     * @return \phpDocumentor\Tutorials
     */
    public function addDirectory($directory)
    {
        if ($directory instanceof \DirectoryIterator) {
            $directory->setFlags(\FilesystemIterator::KEY_AS_FILENAME);
            $this->directories += iterator_to_array(new \CachingIterator($directory));
        }

        return $this;
    }

    public function getCollection()
    {
        return $this->collection === null ? new Tutorials\Collection : $this->collection;
    }

    public function setCollection(Tutorials\Collection $collection)
    {
        $this->collection = $collection;
    }
}