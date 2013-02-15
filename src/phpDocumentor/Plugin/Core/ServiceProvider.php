<?php
namespace phpDocumentor\Plugin\Core;


use Cilex\Application;
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

        $writerCollection['FileIo']     = new \phpDocumentor\Plugin\Core\Transformer\Writer\FileIo();
        $writerCollection['twig']       = new \phpDocumentor\Plugin\Core\Transformer\Writer\Twig();
        $writerCollection['xsl']        = new \phpDocumentor\Plugin\Core\Transformer\Writer\Xsl();
        $writerCollection['Graph']      = new \phpDocumentor\Plugin\Core\Transformer\Writer\Graph();
        $writerCollection['Checkstyle'] = new \phpDocumentor\Plugin\Core\Transformer\Writer\Checkstyle();
        $writerCollection['Sourcecode'] = new \phpDocumentor\Plugin\Core\Transformer\Writer\Sourcecode();
    }
}