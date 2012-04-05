<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category  phpDocumentor
 * @package   Core
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor;

require __DIR__.'/../../vendor/.composer/autoload.php';

use \Symfony\Component\Console\Input\InputInterface;

/**
 * The application base class for phpDocumentor.
 *
 * This class is responsible for initializing phpDocumentor and executing the
 * Console component embedded in Cilex. Since this class extends Cilex (which
 * extends Pimple) you can use it as DI container.
 *
 * This class also contains the (leading) current version number.
 *
 * @category phpDocumentor
 * @package  Core
 * @author   Mike van Riel <mike.vanriel@naenius.com>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT
 * @link     http://phpdoc.org
 */
class Application extends \Cilex\Application
{
    const VERSION = '2.0.0a2';

    /**
     * Initializes Cilex with the correct app name and version.
     */
    public function __construct()
    {
        parent::__construct('phpDocumentor', self::VERSION);
        $this['autoloader']->registerPrefixes(
            array(
                'Zend' =>  array(__DIR__ . '/../../vendor'),
                'ZendX' =>  array(__DIR__ . '/../../vendor'),
            )
        );

        $app = $this;
        $this['event_dispatcher'] = $this->share(function() use ($app){
            return new \Symfony\Component\EventDispatcher\EventDispatcher();
        });

        // register configuration
        $this->register(
            new \Cilex\Provider\ConfigServiceProvider(),
            array(
                'config.path' => 'data/phpdoc.tpl.xml'
            )
        );

        // register logging extension
        $this->register(
            new \Cilex\Provider\MonologServiceProvider(),
            array(
                'monolog.name'    => 'phpDocumentor',
                'monolog.logfile' => '/tmp/phpdoc.log'
            )
        );

        // initialize the plugin manager
//        $this['plugins'] = new \phpDocumentor_Plugin_Manager(
//            $this['event_dispatcher'],
//            $this['config'],
//            $this['autoloader']
//        );
//
//        $this['plugins']->loadFromConfiguration();
    }

    /**
     * Override method to change default command from list to project:run.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return string
     */
    protected function getCommandName(InputInterface $input)
    {
        $name = $input->getFirstArgument('command');
        return $name ? $name : 'project:run';
    }

    public function error()
    {

    }
}
