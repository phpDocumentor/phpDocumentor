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

namespace phpDocumentor\Plugin\Twig;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Provides a series of services that are necessary for Twig to work with phpDocumentor.
 *
 * This provider uses the translator component to fuel the twig writer and ands the to the twig writer to the writer
 * collection. This enables transformations to mention 'twig' as their writer attribute.
 *
 * @see Writer\Twig for more information on using this.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Container $app An Application instance.
     */
    public function register(Container $app): void
    {
        /** @var Translator $translator */
        $translator = $app['translator'];

        /** @var Collection $writerCollection */
        $writerCollection = $app['transformer.writer.collection'];

        $writerCollection['twig'] = new Writer\Twig();
        $writerCollection['twig']->setTranslator($translator);
    }
}
