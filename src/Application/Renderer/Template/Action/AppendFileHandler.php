<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Renderer\Template\Action;

use League\Flysystem\Adapter\Local;
use phpDocumentor\DomainModel\Renderer\Template\Action;
use phpDocumentor\DomainModel\Renderer\Template\ActionHandler;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

class AppendFileHandler implements ActionHandler
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Executes the activities that this Action represents.
     *
     * @param AppendFile $action
     *
     * @todo this method still acts as a Copy because I currently do not know how to append with stream,
     *       fix this as soon as I got internet.
     *
     * @return void
     */
    public function __invoke(Action $action)
    {
        $source      = $this->getSourceLocation($action);
        $destination = $this->getDestination($action);

        Assert::fileExists($source);
        Assert::writable(dirname($destination));

        $destinationFS = $action->getRenderContext()->getFilesystem();

        if (is_file($source)) {
            $stream = fopen($source, 'r+');
            $destinationFS->putStream($destination, $stream);
            fclose($stream);
        } else {
            $sourceFS = new \League\Flysystem\Filesystem(new Local($source));
            foreach ($sourceFS->listContents('', true) as $path) {
                $path = $path['path'];

                if ($sourceFS->getMetadata($path)['type'] === 'dir') {
                    continue;
                }

                $destinationFS->putStream($destination . '/' . $path, $sourceFS->readStream($path));
            }
        }
    }

    /**
     * @param AppendFile $action
     *
     * @return string
     */
    private function getSourceLocation(Action $action)
    {
        $source = (string)$action->getSource();
        if (! $this->filesystem->isAbsolutePath($source)) {
            if (file_exists(getcwd() . '/' . $source)) {
                return getcwd() . '/' . $source;
            }
            if (file_exists(__DIR__ . '/../../../../' . $source)) {
                return __DIR__ . '/../../../../' . $source;
            }

            return __DIR__ . '/../../../../data/' . $source;
        }

        return $source;
    }

    /**
     * @param CopyFile $action
     *
     * @return string
     */
    private function getDestination(Action $action)
    {
        $destination = $action->getRenderContext()->getDestination() . '/' . ltrim($action->getDestination(), '\\/');

        return $destination;
    }
}
