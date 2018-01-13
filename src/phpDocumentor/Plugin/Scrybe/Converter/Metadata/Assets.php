<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata;

use Monolog\Logger;

/**
 * This collection manages which assets were found during the discovery phase.
 *
 * Each asset is represented by an entry containing the path to that asset relative to the project root.
 */
class Assets extends \ArrayObject
{
    /** @var string The root directory of the source documentation. */
    protected $project_root;

    /** @var Logger A logging object used to write informational and debug messages to */
    protected $logger;

    /**
     * Sets the project root for the given assets.
     *
     * @param string $project_root
     *
     * @throws \RuntimeException if the container already contains items.
     */
    public function setProjectRoot($project_root)
    {
        if (count($this) > 0) {
            throw new \RuntimeException('The project root may only be set on an empty asset container');
        }

        $this->project_root = $project_root;
    }

    /**
     * Returns the project root for the given assets.
     *
     * @return string
     */
    public function getProjectRoot()
    {
        return $this->project_root;
    }

    /**
     * Sets an asset to be copied to the given destination path.
     *
     * @param string $source_path
     * @param string $destination_path
     */
    public function set($source_path, $destination_path)
    {
        $this[$source_path] = $destination_path;
    }

    /**
     * Sets a logger with which to record warnings.
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Copies all assets in this collection to their given destination location.
     *
     * @param string $destination
     */
    public function copyTo($destination)
    {
        foreach ($this as $source_path => $asset_path) {
            if (!is_readable($source_path)) {
                if ($this->logger) {
                    $this->logger->error('Asset "' . $source_path . '" could not be found or is not readable');
                }

                continue;
            }

            $destination_path = $destination . '/' . $asset_path;
            if (!file_exists(dirname($destination_path))) {
                mkdir(dirname($destination_path), 0777, true);
            }

            copy($source_path, $destination_path);
        }
    }
}
