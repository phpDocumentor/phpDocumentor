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

namespace phpDocumentor\Parser;

use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Parser\Event\PreFileEvent;
use phpDocumentor\Reflection\FileReflector;
use Psr\Log\LogLevel;

/**
 * Parses a single file into a FileDescriptor and adds it to the given ProjectBuilder.
 */
class File
{
    /** @var Parser  */
    protected $parser;

    /**
     * Registers the Parser object to get settings from.
     *
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses the file identified by the given filename and passes the resulting FileDescriptor to the ProjectBuilder.
     *
     * @param string                   $filename
     * @param ProjectDescriptorBuilder $builder
     *
     * @return void
     */
    public function parse($filename, ProjectDescriptorBuilder $builder)
    {
        if (class_exists('phpDocumentor\Event\Dispatcher')) {
            Dispatcher::getInstance()->dispatch(
                'parser.file.pre',
                PreFileEvent::createInstance($this)->setFile($filename)
            );
        }
        $this->log('Starting to parse file: ' . $filename);

        try {
            $file = $this->createFileReflector($builder, $filename);
            if (!$file) {
                $this->log('>> Skipped file ' . $filename . ' as no modifications were detected');
                return;
            }

            $file->process();
            $builder->buildFileUsingSourceData($file);
            $this->logErrorsForDescriptor($builder->getProjectDescriptor()->getFiles()->get($file->getFilename()));
        } catch (Exception $e) {
            $this->log(
                '  Unable to parse file "' . $filename . '", an error was detected: ' . $e->getMessage(),
                LogLevel::ALERT
            );
        }
    }

    /**
     * Creates a new FileReflector for the given filename or null if the file contains no modifications.
     *
     * @param ProjectDescriptorBuilder $builder
     * @param string                   $filename
     *
     * @return FileReflector|null Returns a new FileReflector or null if no modifications were detected for the given
     *     filename.
     */
    protected function createFileReflector(ProjectDescriptorBuilder $builder, $filename)
    {
        $file = new FileReflector($filename, $this->parser->doValidation(), $this->parser->getEncoding());
        $file->setDefaultPackageName($this->parser->getDefaultPackageName());
        $file->setMarkers($this->parser->getMarkers());
        $file->setFilename($this->getRelativeFilename($filename));

        $cachedFiles = $builder->getProjectDescriptor()->getFiles();
        $hash        = $cachedFiles->get($file->getFilename())
            ? $cachedFiles->get($file->getFilename())->getHash()
            : null;

        return $hash === $file->getHash() && !$this->parser->isForced()
            ? null
            : $file;
    }

    /**
     * Writes the errors found in the Descriptor to the log.
     *
     * @param FileDescriptor $fileDescriptor
     *
     * @return void
     */
    protected function logErrorsForDescriptor(FileDescriptor $fileDescriptor)
    {
        $errors = $fileDescriptor->getAllErrors();

        /** @var Error $error */
        foreach ($errors as $error) {
            $this->log($error->getCode(), $error->getSeverity(), $error->getContext());
        }
    }

    /**
     * Returns the filename, relative to the root of the project directory.
     *
     * @param string $filename The filename to make relative.
     *
     * @throws \InvalidArgumentException if file is not in the project root.
     *
     * @return string
     */
    public function getRelativeFilename($filename)
    {
        // strip path from filename
        $result = ltrim(substr($filename, strlen($this->parser->getPath())), DIRECTORY_SEPARATOR);
        if ($result === '') {
            throw new \InvalidArgumentException(
                'File is not present in the given project path: ' . $filename
            );
        }

        return $result;
    }

    /**
     * Dispatches a logging request.
     *
     * @param string   $message  The message to log.
     * @param string   $priority The logging priority as declared in the LogLevel PSR-3 class.
     * @param string[] $parameters
     *
     * @return void
     */
    protected function log($message, $priority = LogLevel::INFO, $parameters = array())
    {
        Dispatcher::getInstance()->dispatch(
            'system.log',
            LogEvent::createInstance($this->parser)
                ->setContext($parameters)
                ->setMessage($message)
                ->setPriority($priority)
        );
    }
}
