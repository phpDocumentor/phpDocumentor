<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\CommandlineOptionsMiddleware;

final class Configure
{
    /**
     * @var ConfigurationFactory
     */
    private $configFactory;

    /**
     * Configure constructor.
     * @param ConfigurationFactory $configFactory
     */
    public function __construct(ConfigurationFactory $configFactory)
    {
        $this->configFactory = $configFactory;
    }

    public function __invoke(array $options): array
    {
        $this->configFactory->addMiddleware(
            new CommandlineOptionsMiddleware($options)
        );

        return $this->configFactory->get();
    }
}
