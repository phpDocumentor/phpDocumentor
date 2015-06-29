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
use Symfony\Component\Console\Input\InputInterface;

final class LoadTemplates
{
    /** @var Configuration */
    private $configuration;

    /** @var array */
    private $templates;

    /**
     * @param string[]      $templates
     * @param Configuration $configuration
     */
    public function __construct(array $templates, Configuration $configuration)
    {
        $this->templates     = $templates;
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
     * @return array
     */
    public function getTemplates()
    {
        return $this->templates;
    }
}
