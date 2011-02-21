<?php
/**
 * DocBlox
 *
 * @category   DocBlox
 * @package    Parser
 * @copyright  Copyright (c) 2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 */

/**
 * Class representing a single Transformation.
 *
 * @category   DocBlox
 * @package    Parser
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 */
class DocBlox_Transformation extends DocBlox_Abstract
{
  /** @var string */
  protected $query = '';

  /** @var DocBlox_Writer_Abstract */
  protected $writer = null;

  /** @var string */
  protected $source = '';

  /** @var string */
  protected $artifact = '';

  /** @var string[] */
  protected $parameters = array();

  /**
   * Constructs a new Transformation object and populates the required parameters.
   *
   * @param string $query    What information to use as datasource for the writer's source.
   * @param string $writer   What type of transformation to apply (XSLT, PDF, etc).
   * @param string $source   Which template or type of source to use.
   * @param string $artifact What is the filename of the result (relative to the generated root)
   */
  public function __construct($query, $writer, $source, $artifact)
  {
    $this->setQuery($query);
    $this->setWriter($writer);
    $this->setSource($source);
    $this->setArtifact($artifact);
  }

  /**
   * Sets the query.
   *
   * @param string $query
   *
   * @return void
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }

  /**
   * Returns the set query.
   *
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }

  /**
   * Sets the writer type and instantiates a writer.
   *
   * @param string $writer
   *
   * @return void
   */
  public function setWriter($writer)
  {
    $this->writer = DocBlox_Writer_Abstract::getInstanceOf($writer);
  }

  /**
   * Returns an instantiated writer object.
   *
   * @return DocBlox_Writer_Abstract|null
   */
  public function getWriter()
  {
    return $this->writer;
  }

  /**
   * Sets the source / type which the writer will use to generate artifacts from.
   *
   * @param string $source
   *
   * @return void
   */
  public function setSource($source)
  {
    $this->source = $source;
  }

  /**
   * Returns the name of the source / type used in the transformation process.
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Filename of the resulting artifact relative to the root.
   *
   * If the query results in a set of artifacts (multiple nodes / array); then this string must contain an identifying
   * variable as returned by the writer.
   *
   * @param string $artifact
   *
   * @return void
   */
  public function setArtifact($artifact)
  {
    $this->artifact = $artifact;
  }

  /**
   * Returns the name of the artifact.
   *
   * @return string
   */
  public function getArtifact()
  {
    return $this->artifact;
  }

  /**
   * Sets an array of parameters (key => value).
   *
   * @param string[] $parameters
   *
   * @return void
   */
  public function setParameters(array $parameters)
  {
    $this->parameters = $parameters;
  }

  /**
   * Returns all parameters for this transformation.
   *
   * @return string[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }

  /**
   * Executes the transformation.
   *
   * @return void
   */
  public function execute()
  {

  }

}