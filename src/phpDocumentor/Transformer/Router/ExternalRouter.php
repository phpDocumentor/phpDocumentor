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

namespace phpDocumentor\Transformer\Router;

/**
 * Connects class, interface and traits to remote documentation sets.
 */
class ExternalRouter extends RouterAbstract
{
//    /**
//     * Registers the application configuration with this router.
//     *
//     * The configuration is used to extract which external routes to add to the application.
//     */
//    public function __construct(Configuration $configuration)
//    {
//        $this->configuration = $configuration;
//
//        parent::__construct();
//    }

    /**
     * Configuration function to add routing rules to a router.
     */
    public function configure()
    {
        $docs = []; //$this->configuration->getTransformer()->getExternalClassDocumentation();
        foreach ($docs as $external) {
            $prefix = (string) $external->getPrefix();
            $uri = (string) $external->getUri();

            $this[] = new Rule(
                function ($node) use ($prefix) {
                    return is_string($node) && (strpos(ltrim($node, '\\'), $prefix) === 0);
                },
                function ($node) use ($uri) {
                    return str_replace('{CLASS}', ltrim($node, '\\'), $uri);
                }
            );
        }
    }
}
