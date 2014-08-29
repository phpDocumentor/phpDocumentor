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

namespace phpDocumentor\Parser;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Fileset\Collection;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use phpDocumentor\Plugin\Core\Descriptor\Validator\ValidatorAbstract;
use phpDocumentor\Reflection\Event\PostDocBlockExtractionEvent;
use phpDocumentor\Translator\Translator;

/**
 * This provider is responsible for registering the parser component with the given Application.
 */
class ServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * @param Application $app An Application instance
     *
     * @throws Exception\MissingDependencyException if the Descriptor Builder is not present.
     *
     * @return void
     */
    public function register(Application $app)
    {
        if (!isset($app['descriptor.builder'])) {
            throw new Exception\MissingDependencyException(
                'The builder object that is used to construct the ProjectDescriptor is missing'
            );
        }

        $app['parser'] = $app->share(
            function ($app) {
                $parser = new Parser();
                $parser->setStopwatch($app['kernel.stopwatch']);
                $parser->setLogger($app['monolog']);

                return $parser;
            }
        );

        $app['markdown'] = $app->share(
            function () {
                return \Parsedown::instance();
            }
        );

        /** @var Translator $translator  */
        $translator = $app['translator'];
        $translator->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');

        $app['parser.files'] = new Collection();
        $app->command(new ParseCommand($app['descriptor.builder'], $app['parser'], $translator, $app['parser.files']));
    }

    /**
     * Checks all phpDocumentor whether they match the given rules.
     *
     * @param PostDocBlockExtractionEvent $data Event object containing the parameters.
     *
     * @todo convert this method to the new style validators; this method is not invoked anymore
     *
     * @return void
     */
    public function validateDocBlocks($data)
    {
        /** @var \phpDocumentor\Reflection\BaseReflector $element */
        $element = $data->getSubject();

        /** @var \phpDocumentor\Reflection\DocBlock $docblock */
        $docblock = $data->getDocblock();

        // get the type of element
        $type = substr(
            get_class($element),
            strrpos(get_class($element), '\\') + 1,
            -9 // Reflector
        );

        // no docblock, or docblock should be ignored, so no reason to validate
        if ($docblock && $docblock->hasTag('ignore')) {
            return;
        }

        $validatorOptions = $this->loadConfiguration();

        foreach (array('Deprecated', 'Required', $type) as $validator) {

            // todo: move to a factory or builder class
            $class = 'phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\\' . $validator . 'Validator';
            if (class_exists($class)) {
                /** @var ValidatorAbstract $val */
                $val = new $class($element->getName(), $docblock, $element);
                $val->setOptions($validatorOptions);
                $val->isValid($element);
            }
        }
    }

    /**
     * Load the configuration from the plugin.xml file
     *
     * @todo restore required/deprecated validators
     *
     * @return array
     */
    protected function loadConfiguration()
    {
        //$configOptions = $this->plugin->getOptions();
        $validatorOptions = array();

        //foreach (array('deprecated', 'required') as $tag) {
        //    $validatorOptions[$tag] = $this->loadConfigurationByElement($configOptions, $tag);
        //}

        return $validatorOptions;
    }

    /**
     * Load the configuration for given element (deprecated/required)
     *
     * @param array  $configOptions The configuration from the plugin.xml file
     * @param string $configType    Required/Deprecated for the time being
     *
     * @return array
     */
    protected function loadConfigurationByElement($configOptions, $configType)
    {
        $validatorOptions = array();

        if (isset($configOptions[$configType]->tag)) {

            foreach ($configOptions[$configType]->tag as $tag) {
                $tagName = (string) $tag['name'];

                if (isset($tag->element)) {
                    foreach ($tag->element as $type) {
                        $typeName = (string) $type;
                        $validatorOptions[$typeName][] = $tagName;
                    }
                } else {
                    $validatorOptions['__ALL__'][] = $tagName;
                }
            }
        }

        return $validatorOptions;
    }
}
