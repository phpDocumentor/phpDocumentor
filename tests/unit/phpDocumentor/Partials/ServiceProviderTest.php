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

namespace phpDocumentor\Partials;

/**
 * Tests for the partial service provider.
 */
class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {

        $this->markTestIncomplete("Don't know how to mock it.");
        $provider = new ServiceProvider;

        $application['translator'] = '';

        $provider->register();
    }
}
?>