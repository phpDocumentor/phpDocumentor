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
use phpDocumentor\Translator;
use phpDocumentor\Descriptor\Validation;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Classes\HasPackageWithSubpackage;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Classes\HasShortDescription;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Classes\HasSinglePackage;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Classes\HasSingleSubpackage;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Functions\AreAllArgumentsValid;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Functions\IsReturnTypeNotAnIdeDefault;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Generic\HasDocBlock;
use phpDocumentor\Plugin\Core\Descriptor\Validator\Properties\HasShortDescription as PropertyHasShortDescription;
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
        /** @var Translator $translator  */
        $translator = $app['translator'];
        $translator->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');

        /** @var Collection $writerCollection */
        $writerCollection = $app['transformer.writer.collection'];

        $writerCollection['FileIo']     = new Writer\FileIo();
        $writerCollection['twig']       = new Writer\Twig();
        $writerCollection['Graph']      = new Writer\Graph();
        $writerCollection['checkstyle'] = new Writer\Checkstyle();
        $writerCollection['sourcecode'] = new Writer\Sourcecode();
        $writerCollection['xml']        = new Writer\Xml();
        $writerCollection['xsl']        = new Writer\Xsl();

        $writerCollection['checkstyle']->setTranslator($translator);
        $writerCollection['xml']->setTranslator($translator);
        $writerCollection['twig']->setTranslator($translator);
    }
}
