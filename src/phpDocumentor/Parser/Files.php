<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Parser
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

/**
 * Files container handling directory scanning, project root detection and ignores.
 *
 * @category phpDocumentor
 * @package  Parser
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class phpDocumentor_Parser_Files extends phpDocumentor_Parser_Abstract
{
    /** @var \Symfony\Component\Finder\Finder */
    protected $finder = null;

    /** @var bool Whether to follow symlinks*/
    protected $follow_symlinks = false;

    /** @var bool Whether to ignore hidden files and folders */
    protected $ignore_hidden = false;

    /**
     * the glob patterns which directories/files to ignore during parsing and
     * how many files were ignored.
     *
     * Structure of this array is:
     *
     *     array(
     *         0 => <GLOB>
     *         1 => <COUNT of USES>
     *     )
     *
     * @var array[]
     */
    protected $ignore_patterns = array();

    /**
     * @var string[] Array containing a list of allowed line endings;
     *               defaults to php, php3 and phtml.
     */
    protected $allowed_extensions = array('php', 'php3', 'phtml');

    /** @var string[] An array containing the file names which must be processed */
    protected $files = array();

    /** @var string Detected root folder for this project */
    protected $project_root = null;

    /**
     * Initializes the finding component.
     */
    public function __construct()
    {
        $this->finder = new \Symfony\Component\Finder\Finder();
        $this->finder->files();
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
        $this->ignore_patterns = array();

        foreach ($patterns as $pattern) {
            $this->addIgnorePattern($pattern);
        }
    }

    /**
     * Returns the ignore patterns.
     *
     * @return array
     */
    public function getIgnorePatterns()
    {
        return $this->ignore_patterns;
    }

    /**
     * Adds an ignore pattern to the collection.
     *
     * @param string $pattern Glob-like pattern to filter files with.
     *
     * @return void
     */
    public function addIgnorePattern($pattern)
    {
        $this->ignore_patterns[] = $pattern;
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
        $this->allowed_extensions = array();

        foreach ($extensions as $extension) {
            $this->addAllowedExtension($extension);
        }
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
        $this->allowed_extensions[] = $extension;
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
     * @throws InvalidArgumentException if the given path is not a folder.
     *
     * @return void
     */
    public function addDirectory($path)
    {
        $this->finder->in($path);
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
        if (!empty($paths)) {
            // if separate files are provided then the root must always be
            // calculated.
            $this->project_root = null;
        }

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
        $this->finder->append($path);
    }

    /**
     * Returns a list of files that are ready to be parsed.
     *
     * Please note that the ignore pattern will be applied and all files are
     * converted to absolute paths.
     *
     * @return string[]
     */
    public function getFiles()
    {
        $result = array();

        if ($this->follow_symlinks) {
            $this->finder->followLinks();
        }

        $patterns = $this->getIgnorePatterns();
        if (empty($patterns)) {
            $patterns = '';
        } else {
            foreach ($patterns as &$pattern) {
                $this->convertToPregCompliant($pattern);
            }
            $patterns = '/('.implode('|', $patterns).')$/';
        }

        // restrict names to those ending in the given extensions
        $this->finder
            ->name('/\.('.implode('|', $this->allowed_extensions).')$/')
            ->ignoreDotFiles($this->getIgnoreHidden())
            ->filter(
                function(SplFileInfo $file) use ($patterns) {
                    if (!$patterns) {
                        return true;
                    }

                    // apply ignore list on path instead of file, finder
                    // can't do that by default
                    return !preg_match($patterns, $file->getPathname());
                }
            );

        /** @var SplFileInfo $file */
        foreach ($this->finder as $file) {
            $result[] = $file->getRealPath();
        }

        return $result;
    }

    /**
     * Calculates the project root from the given files by determining their
     * highest common path.
     *
     * @return string
     */
    public function getProjectRoot()
    {
        if ($this->project_root === null) {
            $base = '';
            $file = reset($this->files);

            // realpath does not work on phar files
            $file = (substr($file, 0, 7) != 'phar://')
                ? realpath($file)
                : $file;

            $parts = explode(DIRECTORY_SEPARATOR, $file);

            foreach ($parts as $part) {
                $base_part = $base . $part . DIRECTORY_SEPARATOR;
                foreach ($this->files as $dir) {

                    // realpath does not work on phar files
                    $dir = (substr($dir, 0, 7) != 'phar://')
                            ? realpath($dir)
                            : $dir;

                    if (substr($dir, 0, strlen($base_part)) != $base_part) {
                        $this->project_root = $base;
                        return $base;
                    }
                }

                $base = $base_part;
            }

            $this->project_root = $base;
        }

        return $this->project_root;
    }

    /**
     * Converts $string into a string that can be used with preg_match.
     *
     * @param string &$string Glob-like pattern with wildcards ? and *.
     *
     * @author Greg Beaver <cellog@php.net>
     * @author mike van Riel <mike.vanriel@naenius.com>
     *
     * @see PhpDocumentor/phpDocumentor/Io.php
     *
     * @return void
     */
    protected function convertToPregCompliant(&$string)
    {
        $y = (DIRECTORY_SEPARATOR == '\\') ? '\\\\' : '\/';
        $string = str_replace('/', DIRECTORY_SEPARATOR, $string);
        $x = strtr(
            $string,
            array(
                 '?' => '.',
                 '*' => '.*',
                 '.' => '\\.',
                 '\\' => '\\\\',
                 '/' => '\\/',
                 '[' => '\\[',
                 ']' => '\\]',
                 '-' => '\\-'
            )
        );

        if ((strpos($string, DIRECTORY_SEPARATOR) !== false)
            && (strrpos($string, DIRECTORY_SEPARATOR) === strlen($string) - 1)
        ) {
            $x = "(?:.*$y$x?.*|$x.*)";
        }

        $string = $x;
    }

    /**
     * Returns the filename, relative to the root of the project directory.
     *
     * @param string $filename The filename to make relative.
     *
     * @throws InvalidArgumentException if file is not in the project root.
     *
     * @return string
     */
    protected function getRelativeFilename($filename)
    {
        // strip path from filename
        $result = ltrim(
            substr($filename, strlen($this->getProjectRoot())),
            DIRECTORY_SEPARATOR
        );

        if ($result === '') {
            throw new InvalidArgumentException(
                'File is not present in the given project path: ' . $filename
            );
        }

        return $result;
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
     * @throws InvalidArgumentException
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
