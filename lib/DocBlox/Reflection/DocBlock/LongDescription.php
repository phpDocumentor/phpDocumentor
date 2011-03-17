<?php
class DocBlox_Reflection_DocBlock_LongDescription implements Reflector
{
  /** @var string */
  protected $contents = '';

  /** @var DocBlox_Reflection_DocBlock_Tags[] */
  protected $tags = array();

  public function __construct($content)
  {
    if (preg_match('/\{\@(.+?)\}/', $content, $matches))
    {
      array_shift($matches);
      foreach($matches as $tag)
      {
        $this->tags[] = Docblox_Reflection_DocBlock_Tag::createInstance('@'.$tag);
      }
    }

    $this->contents = trim($content);

    // only if we are able to use Markdown, use it.
    // TODO: this should become a more intelligent piece of code where the configuration contains a setting what format
    // long descriptions are.
    if (function_exists('Markdown'))
    {
      $this->contents = Markdown($this->contents);
    }
  }

  /**
   * Returns the text of this description.
   *
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }

  /**
   * Returns a list of tags mentioned in the text.
   *
   * @return DocBlox_Reflection_DocBlock_Tags[]
   */
  public function getTags()
  {
    return $this->tags;
  }

  static public function export()
  {

  }

  public function __toString()
  {
    return $this->getContents();
  }
}