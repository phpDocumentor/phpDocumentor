<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Configuration;

final class MergeConfigurationWithCommandLineOptions
{
    /** @var Configuration */
    private $configuration;

    /** @var string[] */
    private $options;

    /** @var string[] */
    private $arguments;

    public function __construct(Configuration $configuration, array $options, array $arguments = [])
    {
        $this->options = $options;
        $this->arguments = $arguments;
        $this->configuration = $configuration;
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @return \string[]
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return \string[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
