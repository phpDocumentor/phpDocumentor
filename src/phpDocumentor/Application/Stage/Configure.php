<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Application\Configuration\CommandlineOptionsMiddleware;
use phpDocumentor\Application\Configuration\ConfigurationFactory;

final class Configure
{
    /**
     * @var ConfigurationFactory
     */
    private $configFactory;

    /**
     * Configure constructor.
     */
    public function __construct(ConfigurationFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    /**
     * @return string[]
     */
    public function __invoke(array $options): array
    {
        $this->configFactory->addMiddleware(
            new CommandlineOptionsMiddleware($options)
        );

        return $this->configFactory->get();
    }
}
