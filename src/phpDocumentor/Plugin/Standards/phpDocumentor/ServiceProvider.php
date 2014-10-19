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

namespace phpDocumentor\Plugin\Standards\phpDocumentor;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Configuration as ApplicationConfiguration;
use phpDocumentor\Plugin\Standards\Collection;
use phpDocumentor\Plugin\Standards\ServiceProvider as StandardsServiceProvider;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\ArgumentHasParamTag;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\CheckForDuplicatePackage;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\CheckForDuplicateSubpackage;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\CheckForPackageWithSubpackage;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\ParamIsIdeDefault;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\PropertySummaryMissing;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\ReturnIsIdeDefault;
use phpDocumentor\Plugin\Standards\phpDocumentor\Sniff\SummaryMissing;

/**
 * Service Provider responsible for creating all Sniffs and the Ruleset used to validate whether a project matches
 * the phpDocumentor Documentation Standard.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers all sniffs and the phpDocumentor Ruleset.
     *
     * @param Application $app
     *
     * @return void
     */
    public function register(Application $app)
    {
        $this->registerSniffsWithSniffCollection($app);

        $ruleset = new Ruleset();
        $app[StandardsServiceProvider::SERVICE_RULESET_COLLECTION][$ruleset->getName()] = $ruleset;
    }

    /**
     * Extends the Sniff Collection and adds the Sniffs for this Standard.
     *
     * By extending the Sniff Collection instead of directly invoking it we allow other standards to make use of the
     * same sniffs as long as they extend the Sniff Collection as well. It is actually proper behavior for a Service
     * Provider to apply instantiated services by extending the external dependencies.
     *
     * @param Application $app
     *
     * @return void
     */
    private function registerSniffsWithSniffCollection(Application $app)
    {
        if (!isset($app['validator'])) {
            throw new \RuntimeException('The validator manager is missing');
        }

        $app->extend(StandardsServiceProvider::SERVICE_SNIFF_COLLECTION, function (Collection $collection, $container) {
            $validator = $container['validator'];

            foreach (array('File', 'Class', 'Interface', 'Trait') as $type) {
                $collection->addSniff(new SummaryMissing($validator, $type . '.Summary.Missing', $type));
                $collection->addSniff(
                    new CheckForDuplicatePackage($validator, $type . '.Package.CheckForDuplicate', $type)
                );
                $collection->addSniff(
                    new CheckForDuplicateSubpackage($validator, $type . '.Subpackage.CheckForDuplicate', $type)
                );
                $collection->addSniff(
                    new CheckForPackageWithSubpackage($validator, $type . '.Subpackage.CheckForPackage', $type)
                );
            }

            $collection->addSniff(new SummaryMissing($validator, 'Function.Summary.Missing', 'Function'));
            $collection->addSniff(new ReturnIsIdeDefault($validator, 'Function.Return.NotAnIdeDefault', 'Function'));
            $collection->addSniff(new ParamIsIdeDefault($validator, 'Function.Param.NotAnIdeDefault', 'Function'));
            $collection->addSniff(new ArgumentHasParamTag($validator, 'Function.Param.ArgumentInDocBlock', 'Function'));

            $collection->addSniff(new SummaryMissing($validator, 'Method.Summary.Missing', 'Method'));
            $collection->addSniff(new ReturnIsIdeDefault($validator, 'Method.Return.NotAnIdeDefault', 'Method'));
            $collection->addSniff(new ParamIsIdeDefault($validator, 'Method.Param.NotAnIdeDefault', 'Method'));
            $collection->addSniff(new ArgumentHasParamTag($validator, 'Method.Param.ArgumentInDocBlock', 'Method'));

            $collection->addSniff(new PropertySummaryMissing($validator, 'Property.Summary.Missing', 'Property'));

            return $collection;
        });
    }
}
