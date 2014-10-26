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

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Configuration;

/**
 * Connects class, interface and traits to remote documentation sets.
 */
class ExternalRouter extends RouterAbstract
{
    /** @var Configuration */
    protected $configuration;

    /**
     * Registers the application configuration with this router.
     *
     * The configuration is used to extract which external routes to add to the application.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;

        parent::__construct();
    }

    /**
     * Configuration function to add routing rules to a router.
     *
     * @return void
     */
    public function configure()
    {
        $docs = $this->configuration->getTransformer()->getExternalClassDocumentation();
        foreach ($docs as $external) {
            $prefix = (string) $external->getPrefix();
            $uri    = (string) $external->getUri();

            $this[] = new Rule(
                function ($node) use ($prefix) {
                    return (is_string($node) && (strpos(ltrim($node, '\\'), $prefix) === 0));
                },
                function ($node) use ($uri) {
                    return str_replace('{CLASS}', ltrim($node, '\\'), $uri);
                }
            );
        }
    }
}
