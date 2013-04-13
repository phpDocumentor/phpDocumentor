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

namespace phpDocumentor\Plugin\Core;

use Cilex\Application;
use Zend\I18n\Translator\Translator;
use phpDocumentor\Plugin\Core\Transformer\Writer;
use phpDocumentor\Transformer\Writer\Collection;

class ServiceProvider implements \Cilex\ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance.
     */
    public function register(Application $app)
    {
        /** @var Collection $writerCollection */
        $writerCollection = $app['transformer.writer.collection'];

        $writerCollection['FileIo']     = new Writer\FileIo();
        $writerCollection['twig']       = new Writer\Twig();
        $writerCollection['Graph']      = new Writer\Graph();
        $writerCollection['Checkstyle'] = new Writer\Checkstyle();
        $writerCollection['Sourcecode'] = new Writer\Sourcecode();
        $writerCollection['xml']        = new Writer\Xml();
        $writerCollection['xsl']        = new Writer\Xsl();

        /** @var Translator $translator  */
        $translator = $app['translator'];
        $translator->addTranslationFilePattern(
            'phparray',
            __DIR__ . DIRECTORY_SEPARATOR . 'Messages',
            '%s.php'
        );
    }
}
