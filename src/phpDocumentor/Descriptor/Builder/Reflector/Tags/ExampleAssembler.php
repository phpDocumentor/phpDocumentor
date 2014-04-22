<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Builder\Reflector\Tags;

use phpDocumentor\Descriptor\Builder\Reflector\AssemblerAbstract;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Configuration\Files;

class ExampleAssembler extends AssemblerAbstract
{

    /**
     * @var string
     */
    protected static $sourceDirectory = '';

    /**
     * @var string
     */
    protected static $exampleDirectory = '';

    /**
     * Creates a new Descriptor from the given Reflector.
     *
     * @param ExampleTag $data
     *
     * @return ExampleDescriptor
     */
    public function create($data)
    {
        $descriptor = new ExampleDescriptor($data->getName());

        if ($data instanceof ExampleTag) {
            $descriptor->setFilePath((string) $data->getFilePath());
            $descriptor->setStartingLine($data->getStartingLine());
            $descriptor->setLineCount($data->getLineCount());
            $descriptor->setDescription($data->getDescription());
            $descriptor->setExample($this->getExample($data));
        }

        return $descriptor;
    }

	/**
     * @return string
     */
    function getExample($data)
    {
        $filename = $data->getFilePath();

        $file = array();

        if (is_file($this->getExamplePathFromConfig($filename))) {
            $file = file($this->getExamplePathFromConfig($filename));
        } elseif (is_file($this->getExamplePathFromSource($filename))) {
            $file = file($this->getExamplePathFromSource($filename));
        } elseif (is_file($this->getExamplePath($filename))) {
            $file = file($this->getExamplePath($filename));
        } else {
            $file = @file($filename);
        }

        if (empty($file)) {
            $content = "** File not found : {$filename} ** ";
        } else {
            $offset = $data->getStartingLine() - 1;
            $filepart = array_slice($file, $offset, $data->getLineCount());
            $content = implode('', $filepart);
        }

        return $content;
    }
    
    /**
     * Set the Source Directory
     *
     * @param string $directory
     */
    public static function setSourceDirectory($directory = '')
    {
        self::$sourceDirectory = $directory;
    }
    
    /**
     * Get the Source Directory
     *
     * @return string
     */
    public static function getSourceDirectory()
    {
        return self::$sourceDirectory;
    }
    
    /**
     * Set the Examples Directory
     *
     * @param string $directory
     */
    public static function setExampleDirectory($directory = '')
    {
        self::$exampleDirectory = $directory;
    }
    
    /**
     * Get the Examples Directory
     *
     * @return string
     */
    public static function getExampleDirectory()
    {
        return self::$exampleDirectory;
    }
    
    /**
     * Get example filepath based on the example directory inside your project.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePath($file)
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get example filepath based on config.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePathFromConfig($file)
    {
        return rtrim(self::getExampleDirectory(), '\\/') . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get example filepath based on sourcecode.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePathFromSource($file)
    {
        return sprintf(
            '%s%s%s%s%s',
            getcwd(),
            DIRECTORY_SEPARATOR,
            trim(self::getSourceDirectory(), '\\/'),
            DIRECTORY_SEPARATOR,
            trim($file, '"')
        );
    }
}