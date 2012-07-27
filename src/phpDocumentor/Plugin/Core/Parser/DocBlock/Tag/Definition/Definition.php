<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius. (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Core\Parser\DocBlock\Tag\Definition;

use \phpDocumentor\Parser\ParserAbstract;

/**
 * Generic Definition which adds the basic tag information to the structure file.
 *
 * @category   phpDocumentor
 * @package    Parser
 * @subpackage Tag_Definitions
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class Definition extends ParserAbstract
{
    /** @var \SimpleXMLElement */
    protected $xml = null;

    /** @var \phpDocumentor\Reflection\DocBlock\Tag */
    protected $tag = null;

    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @var array
     */
    protected $namespace_aliases = array();

    /**
     * Initializes this object with the given data and sets the name and
     * description.
     *
     * @param string                                 $namespace         Namespace
     *     where this tag occurs.
     * @param string[]                               $namespace_aliases Aliases
     *     used for all namespaces at the location of this tag.
     * @param \SimpleXMLElement                      $xml               XML to
     *     enhance.
     * @param \phpDocumentor\Reflection\DocBlock\Tag $tag               Tag
     *     object to use.
     */
    public function __construct(
        $namespace, $namespace_aliases, \SimpleXMLElement $xml,
        \phpDocumentor\Reflection\DocBlock\Tag $tag
    ) {
        $this->xml = $xml;
        $this->tag = $tag;

        $this->setNamespace($namespace);
        $this->setNamespaceAliases($namespace_aliases);

        $this->setName($this->tag->getName());
        $this->setDescription($this->tag->getDescription());

        if (method_exists($this->tag, 'getTypes')) {
            $this->setTypes($this->tag->getTypes());
        }

        if (method_exists($this->tag, 'getReference')) {
            $this->setReference($this->tag->getReference());
        }
    }

    /**
     * Creates a new instance of this class or one of the specialized sub-classes.
     *
     * @param string                                 $namespace         Namespace
     *     where this tag occurs.
     * @param string[]                               $namespace_aliases Aliases
     *     used for all namespaces at the location of this tag.
     * @param \SimpleXMLElement                      $xml               Root xml
     *     element for this tag.
     * @param \phpDocumentor\Reflection\DocBlock\Tag $tag               The
     *     actual tag as reflected.
     *
     * @todo replace the switch statement with an intelligent container /
     * plugin system.
     *
     * @return Definition
     */
    public static function create(
        $namespace, $namespace_aliases, \SimpleXMLElement $xml,
        \phpDocumentor\Reflection\DocBlock\Tag $tag
    ) {
        $tag_name = $tag->getName();

        // check whether the tag name is namespaced and replace alias with full
        // name to get the FQCN form
        if (strpos($tag_name, '\\') !== false) {
            $tag_name = explode('\\', $tag_name);
            if ((count($tag_name) > 1)
                && isset($namespace_aliases[$tag_name[0]])
            ) {
                $tag_name[0] = $namespace_aliases[$tag_name[0]];
            }

            $tag_name = implode('\\', $tag_name);
            if ($tag_name[0] != '\\') {
                $tag_name = '\\' . $tag_name;
            }
        }

        switch ($tag_name)
        {
        case 'property-write':
        case 'property-read':
        case 'property':
        case 'param':
            $def = new Param(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case 'see':
            $def = new See(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case 'method':
            $def = new Method(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case 'uses':
            $def = new Uses(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case 'covers':
            $def = new Covers(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case 'link':
            $def = new Link(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        case '\Doctrine\ORM\Mapping\column':
        case '\Doctrine\ORM\Mapping\ChangeTrackingPolicy':
        case '\Doctrine\ORM\Mapping\DiscriminatorColumn':
        case '\Doctrine\ORM\Mapping\DiscriminatorMap':
        case '\Doctrine\ORM\Mapping\Entity':
        case '\Doctrine\ORM\Mapping\GeneratedValue':
        case '\Doctrine\ORM\Mapping\HasLifecycleCallbacks':
        case '\Doctrine\ORM\Mapping\Id':
        case '\Doctrine\ORM\Mapping\InheritanceType':
        case '\Doctrine\ORM\Mapping\JoinColumn':
        case '\Doctrine\ORM\Mapping\JoinTable':
        case '\Doctrine\ORM\Mapping\ManyToOne':
        case '\Doctrine\ORM\Mapping\ManyToMany':
        case '\Doctrine\ORM\Mapping\MappedSuperclass':
        case '\Doctrine\ORM\Mapping\OneToOne':
        case '\Doctrine\ORM\Mapping\OneToMany':
        case '\Doctrine\ORM\Mapping\OrderBy':
        case '\Doctrine\ORM\Mapping\PostLoad':
        case '\Doctrine\ORM\Mapping\PostPersist':
        case '\Doctrine\ORM\Mapping\PostRemove':
        case '\Doctrine\ORM\Mapping\PostUpdate':
        case '\Doctrine\ORM\Mapping\PrePersist':
        case '\Doctrine\ORM\Mapping\PreRemove':
        case '\Doctrine\ORM\Mapping\PreUpdate':
        case '\Doctrine\ORM\Mapping\SequenceGenerator':
        case '\Doctrine\ORM\Mapping\Table':
        case '\Doctrine\ORM\Mapping\UniqueConstraint':
        case '\Doctrine\ORM\Mapping\Version':
        case 'Column':
        case 'ChangeTrackingPolicy':
        case 'DiscriminatorColumn':
        case 'DiscriminatorMap':
        case 'Entity':
        case 'GeneratedValue':
        case 'HasLifecycleCallbacks':
        case 'Id':
        case 'InheritanceType':
        case 'JoinColumn':
        case 'JoinTable':
        case 'ManyToOne':
        case 'ManyToMany':
        case 'MappedSuperclass':
        case 'OneToOne':
        case 'OneToMany':
        case 'OrderBy':
        case 'PostLoad':
        case 'PostPersist':
        case 'PostRemove':
        case 'PostUpdate':
        case 'PrePersist':
        case 'PreRemove':
        case 'PreUpdate':
        case 'SequenceGenerator':
        case 'Table':
        case 'UniqueConstraint':
        case 'Version':
            $def = new Doctrine(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        default:
            $def = new self(
                $namespace, $namespace_aliases, $xml, $tag
            );
            break;
        }

        $def->configure();
        return $def;
    }

    /**
     * Hook method where children can extend the structure with extra information.
     *
     * @return void
     */
    protected function configure()
    {
    }

    /**
     * Setter for the name so it can be overridden.
     *
     * @param string $name Name for this definition.
     *
     * @return void
     */
    public function setName($name)
    {
        $this->xml['name'] = $name;
    }

    /**
     * Setter for the reference so it can be overridden.
     *
     * @param string $name Name of the element to which is referred.
     *
     * @return void
     */
    public function setReference($name)
    {
        $this->xml['refers'] = $name;
    }

    /**
     * Setter for the description so it can be overridden.
     *
     * @param string $description Description for this definition.
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->xml['description'] = trim($description);
    }

    /**
     * Sets the namespace for this tag; is used to determine type info.
     *
     * @param string $namespace Namespace name for this definition.
     *
     * @return void
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * Returns the namespace identifier for this tag.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Adds type information to the structure.
     *
     * @param string[] $types Array with types in any format; will be transformed
     *  to FQCN.
     *
     * @todo Move this method to a better spot with namespace and alias access
     *  (together with namespace and alias stuff).
     *
     * @return void
     */
    public function setTypes($types)
    {
        foreach ($types as $type) {
            if ($type == '') {
                continue;
            }

            $type = trim($this->expandType($type));

            // strip ampersands
            $name = str_replace('&', '', $type);
            $type_object = $this->xml->addChild('type', $name);

            // register whether this variable is by reference by checking
            // the first and last character
            $type_object['by_reference'] = ((substr($type, 0, 1) === '&')
                                            || (substr($type, -1) === '&'))
                    ? 'true'
                    : 'false';
        }

        $this->xml['type'] = $this->expandType($this->tag->getType());
    }

    /**
     * Tries to expand a type to it's full namespaced equivalent.
     *
     * @param string $type               Type to expand into full namespaced
     *  equivalent.
     * @param bool   $ignore_non_objects whether to ignore reserved words, when
     *  false it will not expand a set of keywords.
     *
     * @return string
     */
    protected function expandType($type, $ignore_non_objects = false)
    {
        if ($type === null) {
            return null;
        }

        $non_objects = array();
        if (!$ignore_non_objects) {
            $non_objects = array(
                'string', 'int', 'integer', 'bool', 'boolean', 'float', 'double',
                'object', 'mixed', 'array', 'resource', 'void', 'null',
                'callback', 'false', 'true', 'self', '$this', 'callable'
            );
        }
        $namespace = $this->getNamespace() == 'default'
                ? ''
                : $this->getNamespace() . '\\';

        $type = explode('|', $type);
        foreach ($type as &$item) {
            $item = trim($item);

            // add support for array notation
            $is_array = false;
            if (substr($item, -2) == '[]') {
                $item = substr($item, 0, -2);
                $is_array = true;
            }

            if ((substr($item, 0, 1) != '\\')
                && (!in_array(strtolower($item), $non_objects))
            ) {
                $type_parts = explode('\\', $item);

                // if the first part is the keyword 'namespace', replace it
                // with the current namespace
                if ($type_parts[0] == 'namespace') {
                    $type_parts[0] = $this->getNamespace();
                    $item = implode('\\', $type_parts);
                }

                // if the first segment is an alias; replace with full name
                if (isset($this->namespace_aliases[$type_parts[0]])) {
                    $type_parts[0] = $this->namespace_aliases[$type_parts[0]];

                    $item = implode('\\', $type_parts);
                } elseif (count($type_parts) == 1) {
                    // prefix the item with the namespace if there is only one
                    // part and no alias
                    $item = $namespace . $item;
                }
            }

            // full paths always start with a slash
            if (isset($item[0]) && ($item[0] !== '\\')
                && (!in_array(strtolower($item), $non_objects))
            ) {
                $item = '\\' . $item;
            }

            // re-add the array notation markers
            if ($is_array) {
                $item .= '[]';
            }
        }

        return implode('|', $type);
    }

    /**
     * Sets the aliases for all namespaces.
     *
     * @param string[] $namespace_aliases Array of aliases.
     *
     * @return void
     */
    public function setNamespaceAliases($namespace_aliases)
    {
        $this->namespace_aliases = $namespace_aliases;
    }

    /**
     * Returns all aliases for namespaces at the location of this tag.
     *
     * @return array
     */
    public function getNamespaceAliases()
    {
        return $this->namespace_aliases;
    }

}
