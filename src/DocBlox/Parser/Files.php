<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Files container handling directory scanning, project root detection and ignores.
 *
 * @category   DocBlox
 * @package    Parser
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Parser_Files extends DocBlox_Parser_Abstract
{
    /**
     * the glob patterns which directories/files to ignore during parsing and how many files were ignored.
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
     * Sets the patterns by which to detect which files to ignore.
     *
     * @param array $patterns
     *
     * @return void
     */
    public function setIgnorePatterns(array $patterns)
    {
        $this->ignore_patterns = array();

        foreach($patterns as $pattern)
        {
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
        // extract first element; second is a count
        $result = array();
        foreach($this->ignore_patterns as $pattern)
        {
            $result[] = $pattern[0];
        }
        return $result;
    }

    /**
     * Adds an ignore pattern to the collection.
     *
     * @param string $pattern
     *
     * @return void
     */
    public function addIgnorePattern($pattern)
    {
        $this->convertToPregCompliant($pattern);
        $this->ignore_patterns[] = array($pattern, 0);
    }

    /**
     * Sets a list of allowed extensions; if not used php,
     * php3 and phtml is assumed.
     *
     * @param array $extensions
     *
     * @return void
     */
    public function setAllowedExtensions(array $extensions)
    {
        $this->allowed_extensions = array();

        foreach($extensions as $extension)
        {
            $this->addAllowedExtension($extension);
        }
    }

    public function addAllowedExtension($extension)
    {
        $this->allowed_extensions[] = $extension;
    }

    public function addDirectories(array $paths)
    {
        foreach ($paths as $path)
        {
            $this->addDirectory($path);
        }
    }

    public function addDirectory($path)
    {
        // if the given is not a directory, skip it
        if (!is_dir($path)) {
            throw new InvalidArgumentException('Expected the path to a folder but received: '.var_export($path, true));
        }

        // get all files recursively to the files array
        $files_iterator = new RecursiveDirectoryIterator($path);

        // add the CATCH_GET_CHILD option to make sure that an unreadable directory does not halt process but
        // skip that folder
        $recursive_iterator = new RecursiveIteratorIterator(
            $files_iterator, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD
        );

        /** @var SplFileInfo $file */
        foreach ($recursive_iterator as $file) {
            // skipping dots (should any be encountered)
            if (($file->getFilename() == '.') || ($file->getFilename() == '..')) {
                continue;
            }

            // Phar files return false on a call to getRealPath
            $this->addFile(
                (substr($file->getPathname(), 0, 7) != 'phar://')
                    ? $file->getRealPath()
                    : $file->getPathname()
            );
        }
    }

    public function addFiles(array $paths)
    {
        foreach($paths as $path)
        {
            $this->addFile($path);
        }
    }

    public function addFile($path)
    {
        // if it is not a file contained in a phar; check it out with a glob
        if (substr($path, 0, 7) != 'phar://') {
            // search file(s) with the given expressions
            $result = glob($path);
            foreach ($result as $file) {
                // if the path is not a file OR it's extension does not match the given, then do not process it.
                if (
                    !is_file($file) || (!empty($this->allowed_extensions)
                    && !in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $this->allowed_extensions))
                ) {
                    continue;
                }

                $this->files[] = realpath($file);
            }
        } else {
            // only process if it is a file and it matches the allowed extensions
            if (
                is_file($path) && (empty($this->allowed_extensions)
                || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $this->allowed_extensions))
            ) {
                $this->files[] = $path;
            }
        }

        // reset root cache
        $this->project_root = null;
    }

    public function getFiles()
    {
        $result = array();

        foreach($this->files as $filename)
        {
            // check whether this file is ignored; we do this in two steps:
            // 1. Determine whether this is a relative or absolute path, if the
            //    string does not start with *, ?, / or \ then we assume that it is
            //    a relative path
            // 2. check whether the given pattern matches with the filename (or
            //    relative filename in case of a relative comparison)
            foreach ($this->ignore_patterns as $key => $pattern) {
                $glob = $pattern[0];
                if ((($glob[0] !== '*')
                     && ($glob[0] !== '?')
                     && ($glob[0] !== '/')
                     && ($glob[0] !== '\\')
                     && (preg_match(
                            '/^' . $glob . '$/',
                            $this->getRelativeFilename($filename)
                        )))
                    || (preg_match('/^' . $glob . '$/', $filename))
                ) {

                    // increase ignore usage with 1
                    $this->ignore_patterns[$key][1]++;

                    $this->notify(
                        'system.log',
                        '-- File "' . $filename . '" matches ignore pattern, will be skipped'
                    );
                    continue 2;
                }
            }
            $result[] = $filename;
        }

        foreach($this->ignore_patterns as $pattern)
        {
            if ($pattern[1] < 1)
            {
                $this->notify(
                    'system.log',
                    'Ignore pattern "' . $pattern[0] . '" has not been used during processing'
                );
            }
        }

        return $result;
    }

    public function getProjectRoot()
    {
        if ($this->project_root === null)
        {
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
        $result = ltrim(substr($filename, strlen($this->getProjectRoot())), '/');
        if ($result === '') {
            throw new InvalidArgumentException(
                'File is not present in the given project path: ' . $filename
            );
        }

        return $result;
    }

}