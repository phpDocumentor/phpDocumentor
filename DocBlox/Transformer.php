<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Parser
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Core class responsible for transforming the structure.xml file to a set of artifacts.
 *
 * @category   DocBlox
 * @package    Parser
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Transformer extends DocBlox_Abstract
{
  /** @var string|null Target location where to output the artifacts */
  protected $target = null;

  /** @var string|null Location of the structure.xml file */
  protected $source = null;

  /** @var string[] */
  protected $templates = array();

  /** @var Transformation[] */
  protected $transformations = array();

  /**
   * Sets the target location where to output the artifacts.
   *
   * @throws Exception if the target is not a valid writable directory.
   *
   * @param string $target The target location where to output the artifacts.
   *
   * @return void
   */
  public function setTarget($target)
  {
    $path = realpath($target);
    if (!file_exists($path) && !is_dir($path) && !is_writable($path))
    {
      throw new Exception('Given target directory does not exist or is not writable');
    }

    $this->target = $path;
  }

  /**
   * Returns the location where to store the artifacts.
   *
   * @return string
   */
  public function getTarget()
  {
    return $this->target;
  }

  /**
   * Sets the location of the structure file.
   *
   * @throws Exception if the source is not a valid readable file.
   *
   * @param string $source The location of the structure file as full path (may be relative).
   *
   * @return void
   */
  public function setSource($source)
  {
    $path = realpath($source);
    if (!file_exists($path) || !is_readable($path) || !is_file($path))
    {
      throw new Exception('Given source does not exist or is not readable');
    }

    $this->source = $path;
  }

  /**
   * Returns the location of the structure file.
   *
   * @return null|string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Sets one or more templates as basis for the transformations.
   *
   * @param string|string[] $template
   *
   * @return void
   */
  public function setTemplate($template)
  {
    if (!is_array($template))
    {
      $template = array($template);
    }

    $this->templates = $template;
  }

  /**
   * Returns the list of templates which are going to be adopted.
   *
   * @return string[]
   */
  public function getTemplates()
  {
    return $this->templates;
  }

  /**
   * Loads the transformation from the configuration and from the given templates and/or transformations.
   *
   * @param string[] $templates                       Array of template names.
   * @param Transformation[]|array[] $transformations Array of transformations or arrays representing transformations.
   *
   * @see self::addTransformation() for more details regarding the array structure.
   *
   * @return void
   */
  public function loadTransformations(array $templates = array(), array $transformations = array())
  {
    /** @var Zend_Config_Xml[] $config_transformations */
    $config_transformations = $this->getConfig()->get('transformations', array());

    foreach($config_transformations as $transformation)
    {
      switch($transformation->key())
      {
        case 'template':
          $this->addTemplate($transformation['name'], $transformation->toArray());
          break;
        case 'transformation':
          $this->addTransformation($transformation->toArray());
          break;
      }
    }

    array_walk($templates, array($this, 'addTemplate'));
    array_walk($transformations, array($this, 'addTransformation'));
  }

  /**
   * Loads a template by name, if an additional array with details is provided it will try to load parameters from it.
   *
   * @param string        $name
   * @param string[]|null $details
   *
   * @return void
   */
  public function addTemplate($name, $details = null)
  {

  }

  /**
   * Adds the given transformation to the transformer for execution.
   *
   * It is also allowed to pass an array notation for the transformation; then this method will create
   * a transformation object out of it.
   *
   * The structure for this array must be:
   * array(
   *   'query'        => <query>,
   *   'writer'       => <writer>,
   *   'source'       => <source>,
   *   'artifact'     => <artifact>,
   *   'parameters'   => array(<parameters>),
   *   'dependencies' => array(<dependencies>)
   * )
   *
   * @param Transformation|array $transformation
   *
   * @return void
   */
  public function addTransformation($transformation)
  {
    if (is_array($transformation))
    {
      // check if all required items are present
      if (!key_exists('query', $transformation)
        || !key_exists('writer', $transformation)
        || !key_exists('source', $transformation)
        || !key_exists('artifact', $transformation))
      {
        throw new InvalidArgumentException(
          'Transformation array is missing elements, received: ' . var_export($transformation, true)
        );
      }

      $transformation_obj = new DocBlox_Transformation(
        $transformation['query'],
        $transformation['writer'],
        $transformation['source'],
        $transformation['artifact']
      );
      if (isset($transformation['parameters']))
      {
        $transformation_obj->setParameters($transformation['parameters']);
      }

      $transformation = $transformation_obj;
    }

    // if it is still not an object; fail
    if (!is_object($transformation))
    {
      throw new InvalidArgumentException(
        'Only transformations of type (or descended from) DocBlox_Transformation can be used in the '
          . 'transformation process; received: ' . gettype($transformation)
      );
    }

    // if the object is not a DocBlox_Transformation; we cannot use it
    if (!$transformation instanceof DocBlox_Transformation)
    {
      throw new InvalidArgumentException(
        'Only transformations of type (or descended from) DocBlox_Transformation can be used in the '
          . 'transformation process; received: '.get_class($transformation)
      );
    }

    $this->transformations[] = $transformation;
  }

  /**
   * Returns the transformation which this transformer will process.
   *
   * @return Transformation[]
   */
  public function getTransformations()
  {
    return $this->transformations;
  }

  /**
   *
   *
   * @return void
   */
  public function execute()
  {

  }
}