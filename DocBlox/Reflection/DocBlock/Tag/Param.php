<?php
class DocBlox_Reflection_DocBlock_Tag_Param extends DocBlox_Reflection_DocBlock_Tag
{
  /** @var string */
  protected $type = null;

  /**
   * @var string
   */
  protected $variableName = null;

  /**
   * Parses a tag and populates the member variables.
   *
   * @throws DocBlox_Reflection_Exception if an invalid tag line was presented
   *
   * @param string $tag_line Line containing the full tag
   *
   * @return void
   */
  public function __construct($type, $content)
  {
    $this->tag = $type;
    $this->content = $content;
    $content   = preg_split('/\s+/u', $content);

    // if there is only 1, it is either a piece of content or a variable name
    if (count($content) > 1)
    {
      $this->type = array_shift($content);
    }

    // if the next item starts with a $ it must be the variable name
    if ((strlen($content[0]) > 0) && ($content[0]{0} == '$'))
    {
      $this->variableName = array_shift($content);
    }

    $this->description = implode(' ', $content);
  }

  /**
   * Returns the type of the variable.
   *
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }

  /**
   * Returns the variable's name.
   *
   * @return string
   */
  public function getVariableName()
  {
    return $this->variableName;
  }
}