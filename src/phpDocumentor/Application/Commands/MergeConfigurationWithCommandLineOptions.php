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
    /** @var string[] */
    private $options;

    /** @var string[] */
    private $arguments;

    public function __construct(array $options, array $arguments = [])
    {
        $this->options = $options;
        $this->arguments = $arguments;
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
