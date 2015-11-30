<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Reflection\Php\Validation;


use Particle\Validator\ValidationResult;
use phpDocumentor\Reflection\File;

/**
 * This class is a wrapper around the original file class.
 * Which adds the validation results to the actual file.
 */
final class ValidatedFile implements File
{
    /**
     * @var File
     */
    private $wrappedFile;

    /**
     * @var ValidationResult
     */
    private $result;

    /**
     * ValidatedFile constructor.
     * @param File $file real file object.
     * @param ValidationResult $result result of the validator.
     */
    public function __construct(File $file, ValidationResult $result)
    {
        $this->wrappedFile = $file;
        $this->result = $result;
    }

    /**
     * Returns the hash of the contents for this file.
     *
     * @return string
     */
    public function getHash()
    {
        return $this->wrappedFile->getHash();
    }

    /**
     * Retrieves the contents of this file.
     *
     * @return string|null
     */
    public function getSource()
    {
        return $this->wrappedFile->getSource();
    }

    /**
     * Returns the file path relative to the project's root.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->wrappedFile->getPath();
    }

    /**
     * Returns whether or not the validator has validated the values.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->result->isValid();
    }

    /**
     * Returns the array of messages that were collected during validation.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->result->getMessages();
    }

    /**
     * Is triggered when invoking inaccessible methods to proxy them to the wrapped object.
     */
    function __call($name, $arguments)
    {
        return call_user_func(array($this->wrappedFile, $name), $arguments);
    }
}
