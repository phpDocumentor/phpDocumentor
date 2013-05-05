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
        $translator->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');

        $writerCollection['xml']->setTranslator($translator);
        $this->addValidators($app);
    }

    /**
     * Adds validators for the Structural Elements.
     *
     * @param Application $app
     *
     * @return void
     */
    protected function addValidators(Application $app)
    {
        /** @var Validation $validation */
        $validation = $app['descriptor.builder.validator'];
        $validation->register(
            array(
                 Validation::TYPE_FILE,
                 Validation::TYPE_CLASS,
                 Validation::TYPE_INTERFACE,
                 Validation::TYPE_TRAIT,
                 Validation::TYPE_METHOD,
                 Validation::TYPE_PROPERTY,
                 Validation::TYPE_FUNCTION,
                 Validation::TYPE_CONSTANT,
            ),
            array(new HasDocBlock())
        );

        $validation->register(
            array(
                 Validation::TYPE_FILE,
                 Validation::TYPE_CLASS,
                 Validation::TYPE_INTERFACE,
                 Validation::TYPE_TRAIT,
            ),
            array(
                 new HasSinglePackage(),
                 new HasSingleSubpackage(),
                 new HasPackageWithSubpackage(),
            )
        );

        $validation->register(
            array(
                 Validation::TYPE_FILE,
                 Validation::TYPE_CLASS,
                 Validation::TYPE_INTERFACE,
                 Validation::TYPE_TRAIT,
                 Validation::TYPE_METHOD,
                 Validation::TYPE_FUNCTION,
            ),
            array(new HasShortDescription())
        );

        $validation->register(
            array(
                 Validation::TYPE_CONSTANT,
                 Validation::TYPE_PROPERTY,
            ),
            array(new PropertyHasShortDescription())
        );

        $validation->register(
            array(
                 Validation::TYPE_METHOD,
                 Validation::TYPE_FUNCTION,
            ),
            array(
                 new AreAllArgumentsValid(),
                 new IsReturnTypeNotAnIdeDefault(),
            )
        );
    }
}
