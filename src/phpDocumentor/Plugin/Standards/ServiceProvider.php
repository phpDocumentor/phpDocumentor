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

namespace phpDocumentor\Plugin\Standards;

use Cilex\Application;
use Cilex\ServiceProviderInterface;

/**
 * Provides the Services to load and handle Documentation Standards for the Dependency Injection Container.
 *
 * This service provider exposes several services:
 *
 * - `standards.collection` - a listing of all Sniffs that may be enabled by a Rule. Without activation of a Rule the
 *   Sniffs won't do anything.
 * - `standards.collection` - a listing of all available Rulesets.
 * - `standards.ruleset.loader` - an object used to load new Rulesets and enable the correct Sniffs in the collection.
 *
 * This service provider depends on the Symfony Validator (included in the Symfony Validation Component) because a
 * Sniff registers a Validator on a specific class (for phpDocumentor this is always a Descriptor). By invoking the
 * Validator after a Ruleset is loaded we can verify any object.
 *
 * The Violations that result from Validators created by Sniffs will always contain the name of the Rule that enabled
 * the Sniff. The calling application can then search for the associated Rule to determine its Message and Severity
 * using the {@see Ruleset::getRule()) method.
 */
class ServiceProvider implements ServiceProviderInterface
{
    const SERVICE_SNIFF_COLLECTION   = 'standards.collection';
    const SERVICE_RULESET_COLLECTION = 'standards.rulesets';
    const SERVICE_RULESET_LOADER     = 'standards.ruleset.loader';

    /**
     * Registers the services for this provider onto the given container.
     *
     * @param Application $app
     *
     * @see ServiceProvider for a more thorough explanation of the associated services.
     *
     * @throws \RuntimeException if the `validator` service is missing.
     *
     * @return void
     */
    public function register(Application $app)
    {
        if (!isset($app['validator'])) {
            throw new \RuntimeException('The validator manager is missing');
        }

        $app[self::SERVICE_RULESET_COLLECTION] = new \ArrayObject();

        $app[self::SERVICE_SNIFF_COLLECTION] = $app->share(
            function ($app) {
                return new Collection($app['validator']);
            }
        );

        $app[self::SERVICE_RULESET_LOADER] = $app->share(
            function ($app) {
                return new RulesetLoader($app[self::SERVICE_SNIFF_COLLECTION], $app[self::SERVICE_RULESET_COLLECTION]);
            }
        );
    }
}
