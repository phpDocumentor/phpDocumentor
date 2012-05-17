<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Fileset;

/**
 * Files container handling directory scanning, project root detection and ignores.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class Collection extends \ArrayObject
{
    /** @var bool Whether to follow symlinks*/
    protected $follow_symlinks = false;

    /** @var bool Whether to ignore hidden files and folders */
    protected $ignore_hidden = false;

    /** @var Collection\IgnorePatterns */
    protected $ignore_patterns = array();

    /** @var \ArrayObject Array containing a list of allowed line endings */
    protected $allowed_extensions = null;

    /** @var string[] An array containing file names */
    protected $files = array();

    /**
     * Initializes the finding component.
     */
    public function __construct()
    {
        $this->ignore_patterns = new Collection\IgnorePatterns();
        $this->allowed_extensions = new \ArrayObject(
            array('php', 'php3', 'phtml')
        );
    }

    /**
     * Sets the patterns by which to detect which files to ignore.
     *
     * @param array $patterns Glob-like patterns to filter files.
     *
     * @return void
     */
    public function setIgnorePatterns(array $patterns)
    {
        $this->ignore_patterns = new Collection\IgnorePatterns($patterns);
    }

    /**
     * Returns the ignore patterns.
     *
     * @return Collection\IgnorePatterns
     */
    public function getIgnorePatterns()
    {
        return $this->ignore_patterns;
    }

    /**
     * Sets a list of allowed extensions; if not used php, php3 and phtml
     * is assumed.
     *
     * @param array $extensions An array containing extensions to match for.
     *
     * @return void
     */
    public function setAllowedExtensions(array $extensions)
    {
        $this->allowed_extensions = new \ArrayObject($extensions);
    }

    /**
     * Adds a file extension to the list of allowed extensions.
     *
     * No dot is necessary and will even prevent the extension from being
     * picked up.
     *
     * @param string $extension Allowed file Extension to add (i.e. php).
     *
     * @return void
     */
    public function addAllowedExtension($extension)
    {
        $this->allowed_extensions->append($extension);
    }

    /**
     * Adds the content of a set of directories to the list of files to parse.
     *
     * @param array $paths The paths whose contents to add to the collection.
     *
     * @return void
     */
    public function addDirectories(array $paths)
    {
        foreach ($paths as $path) {
            $this->addDirectory($path);
        }
    }

    /**
     * Retrieve all files in the given directory and add them to the parsing list.
     *
     * @param string $path A path to a folder, may be relative, absolute or
     *  even phar.
     *
     * @return void
     */
    public function addDirectory($path)
    {
        $finder = new \Symfony\Component\Finder\Finder();

        $patterns = $this->getIgnorePatterns()->getRegularExpression();

        if ($this->follow_symlinks) {
            $finder->followLinks();
        }

        // restrict names to those ending in the given extensions
        $finder
            ->files()
            ->in($path)
            ->name(
                '/\.('.implode('|', $this->allowed_extensions->getArrayCopy()).')$/'
            )
            ->ignoreDotFiles($this->getIgnoreHidden())
            ->filter(
                function(\SplFileInfo $file) use ($patterns) {
                    if (!$patterns) {
                        return true;
                    }

                    // apply ignore list on path instead of file, finder
                    // can't do that by default
                    return !preg_match($patterns, $file->getPathname());
                }
            );

        try {
            /** @var \SplFileInfo $file */
            foreach ($finder as $file) {
                $file = new File($file);
                $path = $file->getRealPath()
                    ? $file->getRealPath()
                    : $file->getPathname();

                $this[$path] = $file;
            }
        } catch(\LogicException $e)
        {
            // if a logic exception is thrown then no folders were included
            // for phpDocumentor this is not an issue since we accept separate
            // files as well
        }
    }

    /**
     * Adds a list of individual files to the collection.
     *
     * @param array $paths File locations, may be absolute, relative or even phar.
     *
     * @return void
     */
    public function addFiles(array $paths)
    {
        foreach ($paths as $path) {
            $this->addFile($path);
        }
    }

    /**
     * Adds a file to the collection.
     *
     * @param string $path File location, may be absolute, relative or even phar.
     *
     * @return void
     */
    public function addFile($path)
    {
        foreach (glob($path) as $path) {
            $file = new File($path);
            $path = $file->getRealPath()
                ? $file->getRealPath()
                : $file->getPathname();

            $this[$path] = $file;
        }
    }

    /**
     * Returns a list of file paths that are ready to be parsed.
     *
     * Please note that the ignore pattern will be applied and all files are
     * converted to absolute paths.
     *
     * @return string[]
     */
    public function getFilenames()
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * Calculates the project root from the given files by determining their
     * highest common path.
     *
     * @return string
     */
    public function getProjectRoot()
    {
        $base = '';
        $files = array_keys($this->getArrayCopy());
        $parts = explode(DIRECTORY_SEPARATOR, reset($files));

        foreach ($parts as $part) {
            $base_part = $base . $part . DIRECTORY_SEPARATOR;
            foreach ($files as $dir) {
                if (substr($dir, 0, strlen($base_part)) != $base_part) {
                    return $base;
                }
            }

            $base = $base_part;
        }

        return $base;
    }

    /**
     * Sets whether to ignore hidden files and folders.
     *
     * @param boolean $ignore_hidden if true skips hidden files and folders.
     *
     * @return void
     */
    public function setIgnoreHidden($ignore_hidden)
    {
        $this->ignore_hidden = $ignore_hidden;
    }

    /**
     * Returns whether files and folders that are hidden are ignored.
     *
     * @return boolean
     */
    public function getIgnoreHidden()
    {
        return $this->ignore_hidden;
    }

    /**
     * Sets whether to follow symlinks.
     *
     * PHP version 5.2.11 is at least required since the
     * RecursiveDirectoryIterator does not support the FOLLOW_SYMLINKS
     * constant before that version.
     *
     * @param boolean $follow_symlinks
     *
     * @return void
     */
    public function setFollowSymlinks($follow_symlinks)
    {
        $this->follow_symlinks = $follow_symlinks;
    }

    /**
     * Returns whether to follow symlinks.
     *
     * @return boolean
     */
    public function getFollowSymlinks()
    {
        return $this->follow_symlinks;
    }

}
