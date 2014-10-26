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

namespace phpDocumentor\Plugin\Scrybe\Template;

use Symfony\Component\Finder\Finder;

/**
 * Template class to use Twig to generate templates.
 */
class Twig implements TemplateInterface
{
    /** @var string The base location for templates */
    protected $path = '';

    /** @var string The name of the containing template folder */
    protected $name = 'default';

    /** @var string The extension used to select the correct template file */
    protected $extension = 'html';

    /**
     * Constructs the twig template and sets the default values.
     *
     * @param string $templatePath the base location for templates.
     */
    public function __construct($templatePath)
    {
        $this->path = $templatePath;
    }

    /**
     * Sets the name for this template.
     *
     * @param string $name A template name that may be composed of alphanumeric characters, underscores and/or hyphens.
     *
     * @throws \InvalidArgumentException if the name does not match the prescribed format.
     *
     * @return void
     */
    public function setName($name)
    {
        if (!preg_match('/^[0-9a-zA-Z\-\_]{3,}$/', $name)) {
            throw new \InvalidArgumentException(
                'A template name may only be composed of alphanumeric '
                .'characters, underscores or hyphens and have at least 3 '
                .'characters.'
            );
        }

        $this->name = $name;
    }

    /**
     * Returns the name of this template.
     *
     * See setName() for a specification of the format.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the base path where the templates are stored.
     *
     * @param string $path
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function setPath($path)
    {
        if (!file_exists($path) || !is_dir($path)) {
            throw new \InvalidArgumentException(
                'Expected the template path to be an existing directory, received: '.$path
            );
        }

        $this->path = $path;
    }

    /**
     * Returns the base path where the templates are stored.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the file extension used to determine the template filename.
     *
     * The file extension of the destination format needs to be set. This is used to retrieve the correct template.
     *
     * @param string $extension an extension (thus only containing alphanumeric characters and be between 2 and 4
     *     characters in size).
     *
     * @throws \InvalidArgumentException if the extension does not match the validation restrictions mentioned above.
     *
     * @return void
     */
    public function setExtension($extension)
    {
        if (!preg_match('/^[a-zA-Z0-9]{2,4}$/', $extension)) {
            throw new \InvalidArgumentException(
                'Extension should be only be composed of alphanumeric characters'
                . ' and should be at least 2 but no more than 4 characters'
            );
        }
        $this->extension = $extension;
    }

    /**
     * Returns the extension of the destination file extension.
     *
     * @see setExtension for more information and the format of the extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Applies the relevant template upon the given content.
     *
     * This method takes the combines the template with the given contents and generates a final piece of text
     * from that.
     *
     * The user may add additional options that are set as parameters in the template.
     *
     * @param string   $contents
     * @param string[] $options
     *
     * @see getTemplateFileName() how the filename is assembled
     *
     * @return string
     */
    public function decorate($contents, array $options = array())
    {
        return $this->getTwigEnvironment()->render(
            $this->getTemplateFilename(),
            array_merge(array('contents' => $contents), $options)
        );
    }

    /**
     * Returns a list of files that need to be copied to the destination location.
     *
     * Examples of assets can be:
     *
     * * CSS files
     * * Javascript files
     * * Images
     *
     * Assets for this template engine means every file that is contained in a subfolder of the template folder and
     * does not end with the extension twig.
     *
     * Thus every file in the root of the template folder is ignored and files and directories having only twig
     * templates (considered as being includes) are not included in this list.
     *
     * @return string[]
     */
    public function getAssets()
    {
        $finder = new Finder();

        return iterator_to_array(
            $finder->files()
                ->in($this->path.DIRECTORY_SEPARATOR . $this->name)
                ->depth('> 0')
                ->notName('*.twig')
                ->sortByName()
        );
    }

    /**
     * Returns the filename for the template.
     *
     * The filename is composed of the following components:
     *
     * - the template base folder
     * - the template's name
     * - a path separator
     * - the literal 'layout' combined with the extension
     * - and as final extension the literal '.twig'
     *
     * @throws \DomainException if the template does not exist.
     *
     * @return string
     */
    protected function getTemplateFilename()
    {
        $filename = $this->name.'/layout.' . $this->extension . '.twig';

        $template_path = $this->path . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($template_path)) {
            throw new \DomainException('Template file "' . $template_path . '" could not be found');
        }

        return $filename;
    }

    /**
     * Constructs and returns the twig environment.
     *
     * This uses the path as defined with this class to instantiate a new Environment and disables the escaping
     * mechanism since we use it to generate HTML; even embedded.
     *
     * @see $path for the template base path.
     *
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Filesystem($this->path), array('autoescape' => false));

        return $twig;
    }
}
