<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Descriptor\FileDescriptor;
use Zend\Config\Config;

/**
 * The default router for phpDocumentor.
 */
class ExternalRouter extends RouterAbstract
{
    /** @var Config */
    protected $configuration;

    public function __construct(Config $configuration)
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
        if (!isset($this->configuration->transformer->{'external-class-documentation'})) {
            return;
        }

        $docs = $this->configuration->transformer->{'external-class-documentation'};
        if (isset($docs->prefix)) {
            $docs = array($docs);
        }

        foreach ((array)$docs as $external) {
            $prefix = (string)$external->prefix;
            $uri    = (string)$external->uri;

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
