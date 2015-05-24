<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Twig;

use phpDocumentor\Transformer\Writer\Collection;

/**
 * Provides a series of services that are necessary for Twig to work with phpDocumentor.
 *
 * This provider uses the translator component to fuel the twig writer and ands the to the twig writer to the writer
 * collection. This enables transformations to mention 'twig' as their writer attribute.
 *
 * @see Writer\Twig for more information on using this.
 */
class ServiceProvider
{
    /** @var Collection */
    private $collection;

    /** @var Writer\Twig */
    private $twigWriter;

    public function __construct(Collection $collection, Writer\Twig $twigWriter)
    {
        $this->collection = $collection;
        $this->twigWriter = $twigWriter;
    }

    /**
     * Registers services on the given app.
     */
    public function __invoke()
    {
        $this->collection['twig'] = $this->twigWriter;
    }
}
