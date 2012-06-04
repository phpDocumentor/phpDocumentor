<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
namespace phpDocumentor\Plugin\Core;

use phpDocumentor\Plugin\ListenerAbstract;

/**
 * Listener for the Core Plugin.
 *
 * @category   phpDocumentor
 * @package    Plugin
 * @subpackage Core
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Listener extends ListenerAbstract
{
    /**
     * Applies all behaviours prior to transformation.
     *
     * @param \sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event transformer.transform.pre
     *
     * @return void
     */
    public function applyBehaviours(\sfEvent $data)
    {
        if (!$data->getSubject() instanceof \phpDocumentor\Transformer\Transformer) {
            throw new Exception(
                'Unable to apply behaviours, the invoking object is not a '
                . '\phpDocumentor\Transformer\Transformer'
            );
        }

        $behaviours = new Transformer\Behaviour\Collection(
            $data->getSubject(),
            array(
                 new Transformer\Behaviour\GeneratePaths(),
                 new Transformer\Behaviour\Inherit(),
                 new Transformer\Behaviour\Tag\IgnoreTag(),
                 new Transformer\Behaviour\Tag\ReturnTag(),
                 new Transformer\Behaviour\Tag\ParamTag(),
                 new Transformer\Behaviour\Tag\VarTag(),
                 new Transformer\Behaviour\Tag\PropertyTag(),
                 new Transformer\Behaviour\Tag\MethodTag(),
                 new Transformer\Behaviour\Tag\UsesTag(),
                 new Transformer\Behaviour\Tag\CoversTag(),
                 new Transformer\Behaviour\Tag\AuthorTag(),
                 new Transformer\Behaviour\Tag\LicenseTag(),
                 new Transformer\Behaviour\Tag\InternalTag(),
                 new Transformer\Behaviour\AddLinkInformation(),
            )
        );

        $data['source'] = $behaviours->process($data['source']);
    }

    /**
     * Checks all phpDocumentor whether they match the given rules.
     *
     * @param \sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event reflection.docblock-extraction.post
     *
     * @return void
     */
    public function validateDocBlocks(\sfEvent $data)
    {
        /** @var \phpDocumentor\Reflection\BaseReflector $element  */
        $element = $data->getSubject();

        /** @var \phpDocumentor\Reflection\DocBlock $docblock  */
        $docblock = $data['docblock'];

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

            $class = 'phpDocumentor\Plugin\Core\Parser\DocBlock\Tag\Validator\\'
                . $validator.'Validator';

            if (class_exists($class)) {
                /** @var Parser\DocBlock\Tag\Validator\ValidatorAbstract $val */
                $val = new $class(
                    $this->plugin,
                    $element->getName(),
                    $docblock,
                    $element
                );

                $val->setOptions($validatorOptions);
                $val->isValid();
            }
        }
    }

    /**
     * Prepare the tag to be injected into the XML file.
     *
     * @param \sfEvent $data Event object containing the parameters.
     *
     * @phpdoc-event reflection.docblock.tag.export
     *
     * @return void
     */
    public function exportTag(\sfEvent $data)
    {
        /** @var \phpDocumentor\Reflection\BaseReflector $subject  */
        $subject = $data->getSubject();

        Parser\DocBlock\Tag\Definition\Definition::create(
            $subject->getNamespace(),
            $subject->getNamespaceAliases(),
            $data['xml'],
            $data['object']
        );
    }

    /**
     * Load the configuration from the plugin.xml file
     *
     * @return array
     */
    protected function loadConfiguration()
    {
        $configOptions = $this->plugin->getOptions();
        $validatorOptions = array();

        foreach (array('deprecated', 'required') as $tag) {
            $validatorOptions[$tag] = $this->loadConfigurationByElement(
                $configOptions, $tag
            );
        }

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
                $tagName = (string)$tag['name'];

                if (isset($tag->element)) {
                    foreach ($tag->element as $type) {
                        $typeName = (string)$type;
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
