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

use phpDocumentor\Descriptor\DescriptorAbstract;

/**
 * Proxies a generated Routing Rule to generate physical filenames.
 *
 * By default a RoutingRule will generate a relative path on a webserver. This causes
 * issues between operating systems since Linux uses / and Windows \ as a directory
 * separator.
 *
 * To make sure that the correct file is generated can this proxy be used to generate
 * a filename instead of a webserver path.
 */
class ForFileProxy
{
    /** @var Rule $rule Contains the Routing Rule that is wrapped by this proxy */
    protected $rule;

    /**
     * Registers the Routing Rule that needs to be translated with this proxy.
     *
     * @param Rule $rule
     */
    public function __construct(Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Generates an URL for the given node.
     *
     * @param string|DescriptorAbstract $node               The node for which to generate an URL.
     * @param string                    $directorySeparator Which directory separator should be used to generate the
     *     paths with, defaults to the default separator for the current O/S.
     *
     * @return string|false a well-formed relative or absolute URL, or false if no URL could be generated.
     */
    public function generate($node, $directorySeparator = DIRECTORY_SEPARATOR)
    {
        $webserverPath = $this->rule->generate($node);

        return $webserverPath === false ? false : str_replace('/', $directorySeparator, $webserverPath);
    }
}
