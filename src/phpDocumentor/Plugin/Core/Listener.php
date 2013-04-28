<?php
///**
// * phpDocumentor
// *
// * PHP Version 5.3
// *
// * @author    Mike van Riel <mike.vanriel@naenius.com>
// * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
// * @license   http://www.opensource.org/licenses/mit-license.php MIT
// * @link      http://phpdoc.org
// */
//namespace phpDocumentor\Plugin\Core;
//
//use phpDocumentor\Plugin\ListenerAbstract;
//use phpDocumentor\Transformer\Event\PreTransformationEvent;
//use phpDocumentor\Reflection\Event\PostDocBlockExtractionEvent;
//use phpDocumentor\Reflection\Event\ExportDocBlockTagEvent;
//
///**
// * Listener for the Core Plugin.
// */
//class Listener extends ListenerAbstract
//{
//    /**
//     * Applies all behaviours prior to transformation.
//     *
//     * @param PreTransformationEvent $data Event object containing the parameters.
//     *
//     * @phpdoc-event transformer.transform.pre
//     *
//     * @throws Exception if the event does not originate from the Transformer
//     *
//     * @return void
//     */
//    public function applyBehaviours($data)
//    {
////        if (!$data->getSubject() instanceof \phpDocumentor\Transformer\Transformer) {
////            throw new Exception(
////                'Unable to apply behaviours, the invoking object is not a '
////                . '\phpDocumentor\Transformer\Transformer'
////            );
////        }
////
////        $behaviour_objects = array(
////            new Transformer\Behaviour\GeneratePaths(),
////            new Transformer\Behaviour\Inherit(),
////            new Transformer\Behaviour\Tag\IgnoreTag(),
////            new Transformer\Behaviour\Tag\ReturnTag(),
////            new Transformer\Behaviour\Tag\ParamTag(),
////            new Transformer\Behaviour\Tag\VarTag(),
////            new Transformer\Behaviour\Tag\PropertyTag(),
////            new Transformer\Behaviour\Tag\MethodTag(),
////            new Transformer\Behaviour\Tag\UsesTag(),
////            new Transformer\Behaviour\Tag\CoversTag(),
////            new Transformer\Behaviour\Tag\AuthorTag(),
////            new Transformer\Behaviour\Tag\LicenseTag(),
////            new Transformer\Behaviour\Tag\InternalTag(),
////            new Transformer\Behaviour\AddLinkInformation(),
////        );
////
////        $behaviours = new \phpDocumentor\Transformer\Behaviour\Collection($data->getSubject(), $behaviour_objects);
////        $data->setSource($behaviours->process($data->getSource()));
//    }
//
//    /**
//     * Checks all phpDocumentor whether they match the given rules.
//     *
//     * @param PostDocBlockExtractionEvent $data Event object containing the
//     *     parameters.
//     *
//     * @phpdoc-event reflection.docblock-extraction.post
//     *
//     * @return void
//     */
//    public function validateDocBlocks($data)
//    {
//        /** @var \phpDocumentor\Reflection\BaseReflector $element  */
//        $element = $data->getSubject();
//
//        /** @var \phpDocumentor\Reflection\DocBlock $docblock  */
//        $docblock = $data->getDocblock();
//
//        // get the type of element
//        $type = substr(
//            get_class($element),
//            strrpos(get_class($element), '\\') + 1,
//            -9 // Reflector
//        );
//
//        // no docblock, or docblock should be ignored, so no reason to validate
//        if ($docblock && $docblock->hasTag('ignore')) {
//            return;
//        }
//
//        $validatorOptions = $this->loadConfiguration();
//
//        foreach (array('Deprecated', 'Required', $type) as $validator) {
//
//            // todo: move to a factory or builder class
//            $class = 'phpDocumentor\Plugin\Core\Parser\DocBlock\Validator\\'
//                . $validator.'Validator';
//
//            if (class_exists($class)) {
//                /** @var Parser\DocBlock\Validator\ValidatorAbstract $val */
//                $val = new $class(
//                    $this->plugin,
//                    $element->getName(),
//                    $docblock,
//                    $element
//                );
//
//                $val->setOptions($validatorOptions);
//                $val->isValid();
//            }
//        }
//    }
//
//    /**
//     * Load the configuration from the plugin.xml file
//     *
//     * @return array
//     */
//    protected function loadConfiguration()
//    {
//        $configOptions = $this->plugin->getOptions();
//        $validatorOptions = array();
//
//        foreach (array('deprecated', 'required') as $tag) {
//            $validatorOptions[$tag] = $this->loadConfigurationByElement($configOptions, $tag);
//        }
//
//        return $validatorOptions;
//    }
//
//    /**
//     * Load the configuration for given element (deprecated/required)
//     *
//     * @param array  $configOptions The configuration from the plugin.xml file
//     * @param string $configType    Required/Deprecated for the time being
//     *
//     * @return array
//     */
//    protected function loadConfigurationByElement($configOptions, $configType)
//    {
//        $validatorOptions = array();
//
//        if (isset($configOptions[$configType]->tag)) {
//
//            foreach ($configOptions[$configType]->tag as $tag) {
//                $tagName = (string)$tag['name'];
//
//                if (isset($tag->element)) {
//                    foreach ($tag->element as $type) {
//                        $typeName = (string)$type;
//                        $validatorOptions[$typeName][] = $tagName;
//                    }
//                } else {
//                    $validatorOptions['__ALL__'][] = $tagName;
//                }
//            }
//        }
//
//        return $validatorOptions;
//    }
//}
