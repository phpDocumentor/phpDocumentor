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

namespace phpDocumentor\Parser;

use Cilex\Application;
use Cilex\ServiceProviderInterface;
use phpDocumentor\Partials\Collection as PartialsCollection;
use phpDocumentor\Translator;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\ValidatorAbstract;
use phpDocumentor\Reflection\Event\PostDocBlockExtractionEvent;

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
     * @throws Exception\MissingNameForPartialException if a partial has no name provided.
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
            function () {
                return new Parser();
            }
        );

        $app['markdown'] = $app->share(
            function () {
                return new \dflydev\markdown\MarkdownExtraParser;
            }
        );

        /** @var Translator $translator  */
        $translator = $app['translator'];
        $translator->addTranslationFolder(__DIR__ . DIRECTORY_SEPARATOR . 'Messages');

        $config = $app['config']->toArray();

        if (isset($config['partials'])) {
            $partialsCollection = new PartialsCollection($app['markdown']);

            $partials = is_array(current($config['partials']['partial']))
                ? $config['partials']['partial']
                : array($config['partials']['partial']);

            foreach($partials as $partial) {
                if (!isset($partial['name'])) {
                    throw new Exception\MissingNameForPartialException(
                       'The name of the partial to load is missing'
                    );
                }
                if (isset($partial['content'])) {
                    $partialsCollection->set($partial['name'], $partial['content']);
                } elseif(isset($partial['link'])) {
                    if (!is_readable($partial['link'])) {
                        $app['monolog']->error(
                            sprintf($translator->translate('PPCPP:EXC-NOPARTIAL'), $partial['link'])
                        );
                    } else {
                        $partialsCollection->set($partial['name'], file_get_contents($partial['link']));
                    }
                } else {
                    $partialsCollection->set($partial['name'], '');
                }
            }

            $app['partials'] = $partialsCollection;
        }

        $app->command(new ParseCommand($app['descriptor.builder'], $app['parser'], $translator));

        /** @var Dispatcher $dispatcher  */
        $dispatcher = $app['event_dispatcher'];
        $dispatcher->addListener('reflection.docblock-extraction.post', array($this, 'validateDocBlocks'));
    }

    /**
     * Checks all phpDocumentor whether they match the given rules.
     *
     * @param PostDocBlockExtractionEvent $data Event object containing the parameters.
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
                $val->isValid();
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
