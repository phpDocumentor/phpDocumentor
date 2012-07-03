<?php
namespace phpDocumentor\Plugin\Twig;

use \phpDocumentor\Transformer\Transformation;

class Extension extends \Twig_Extension implements ExtensionInterface
{
    /**
     * @var \SimpleXMLElement
     */
    protected $data = null;
    protected $destination = '';

    public function __construct(
        \SimpleXMLElement $structure, Transformation $transformation
    ) {
        $this->data = $structure;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    public function getDestination()
    {
        return $this->destination;
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    function getName()
    {
        return 'phpdocumentor';
    }

    public function getFunctions()
    {
        return array(
            'path' => new \Twig_Function_Method($this, 'convertToRootPath'),
        );
    }

    public function convertToRootPath($relative_path)
    {
        // get the path to the root directory
        $path_parts = explode('/', $this->getDestination());
        if (count($path_parts) > 1) {
            $path_to_root = implode(
                '/', array_fill(0, count($path_parts) -1, '..')
            ).'/';
        } else {
            $path_to_root = '';
        }

        // append the relative path to the root
        return $path_to_root.ltrim($relative_path, '/');
    }
}
