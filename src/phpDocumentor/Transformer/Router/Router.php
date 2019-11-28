<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use ArrayObject;
use InvalidArgumentException;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\FunctionDescriptor;
use phpDocumentor\Descriptor\InterfaceDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\TraitDescriptor;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen;
use phpDocumentor\Reflection\DocBlock\Tags\Reference\Url;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Pathfinder;
use UnexpectedValueException;

/**
 * The default for phpDocumentor.
 */
class Router extends ArrayObject
{
    private $projectDescriptorBuilder;
    private $namespaceUrlGenerator;
    private $fileUrlGenerator;
    private $packageUrlGenerator;
    private $classUrlGenerator;
    private $methodUrlGenerator;
    private $constantUrlGenerator;
    private $functionUrlGenerator;
    private $propertyUrlGenerator;
    private $fqsenUrlGenerator;

    public function __construct(
        ProjectDescriptorBuilder $projectDescriptorBuilder,
        UrlGenerator\NamespaceDescriptor $namespaceUrlGenerator,
        UrlGenerator\FileDescriptor $fileUrlGenerator,
        UrlGenerator\PackageDescriptor $packageUrlGenerator,
        UrlGenerator\ClassDescriptor $classUrlGenerator,
        UrlGenerator\MethodDescriptor $methodUrlGenerator,
        UrlGenerator\ConstantDescriptor $constantUrlGenerator,
        UrlGenerator\FunctionDescriptor $functionUrlGenerator,
        UrlGenerator\PropertyDescriptor $propertyUrlGenerator,
        UrlGenerator\FqsenDescriptor $fqsenUrlGenerator
    ) {
        $this->projectDescriptorBuilder = $projectDescriptorBuilder;
        $this->namespaceUrlGenerator = $namespaceUrlGenerator;
        $this->fileUrlGenerator = $fileUrlGenerator;
        $this->packageUrlGenerator = $packageUrlGenerator;
        $this->classUrlGenerator = $classUrlGenerator;
        $this->methodUrlGenerator = $methodUrlGenerator;
        $this->constantUrlGenerator = $constantUrlGenerator;
        $this->functionUrlGenerator = $functionUrlGenerator;
        $this->propertyUrlGenerator = $propertyUrlGenerator;
        $this->fqsenUrlGenerator = $fqsenUrlGenerator;

        parent::__construct();
        $this->configure();
    }

    /**
     * Configuration function to add routing rules to a router.
     */
    public function configure()
    {
        $projectDescriptorBuilder = $this->projectDescriptorBuilder;

        // Here we cheat! If a string element is passed to this rule then we try to transform it into a Descriptor
        // if the node is translated we do not let it match and instead fall through to one of the other rules.
        $stringRule = function (&$node) use ($projectDescriptorBuilder) {
            $elements = $projectDescriptorBuilder->getProjectDescriptor()->getIndexes()->get('elements');
            if (is_string($node) && isset($elements[$node])) {
                $node = $elements[$node];
            }

            return false;
        };

        // @codingStandardsIgnoreStart
        $this[] = new Rule($stringRule, function () {
            return false;
        });
        $this[] = new Rule(function ($node) {
            return $node instanceof FileDescriptor;
        }, $this->fileUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PackageDescriptor;
        }, $this->packageUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof TraitDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof NamespaceDescriptor;
        }, $this->namespaceUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof InterfaceDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ClassDescriptor;
        }, $this->classUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof ConstantDescriptor;
        }, $this->constantUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof MethodDescriptor;
        }, $this->methodUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof FunctionDescriptor;
        }, $this->functionUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof PropertyDescriptor;
        }, $this->propertyUrlGenerator);
        $this[] = new Rule(function ($node) {
            return $node instanceof Fqsen;
        }, $this->fqsenUrlGenerator);

        // if this is a link to an external page; return that URL
        $this[] = new Rule(
            function ($node) {
                return $node instanceof Url;
            },
            function ($node) {
                return (string) $node;
            }
        );

        // do not generate a file for every unknown type
        $this[] = new Rule(function () {
            return true;
        }, function () {
            return false;
        });
        // @codingStandardsIgnoreEnd
    }

    /**
     * Tries to match the provided node with one of the rules in this router.
     *
     * @param string|DescriptorAbstract $node
     *
     * @return Rule|null
     */
    public function match($node)
    {
        /** @var Rule $rule */
        foreach ($this as $rule) {
            if ($rule->match($node)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Uses the currently selected node and transformation to assemble the destination path for the file.
     *
     * Writers accept the use of a Query to be able to generate output for multiple objects using the same
     * template.
     *
     * The given node is the result of such a query, or if no query given the selected element, and the transformation
     * contains the destination file.
     *
     * Since it is important to be able to generate a unique name per element can the user provide a template variable
     * in the name of the file.
     * Such a template variable always resides between double braces and tries to take the node value of a given
     * query string.
     *
     * Example:
     *
     *   An artifact stating `classes/{{name}}.html` will try to find the
     *   node 'name' as a child of the given $node and use that value instead.
     *
     * @throws InvalidArgumentException if no artifact is provided and no routing rule matches.
     * @throws UnexpectedValueException if the provided node does not contain anything.
     *
     * @return null|string returns the destination location or false if generation should be aborted.
     */
    public function destination(Descriptor $descriptor, Transformation $transformation): ?string
    {
        $path = $transformation->getTransformer()->getTarget() . DIRECTORY_SEPARATOR . $transformation->getArtifact();
        if (!$transformation->getArtifact()) {
            $rule = $this->match($descriptor);
            if (!$rule) {
                throw new InvalidArgumentException(
                    'No matching routing rule could be found for the given node, please provide an artifact location, '
                    . 'encountered: ' . get_class($descriptor)
                );
            }

            $rule = new ForFileProxy($rule);
            $url = $rule->generate($descriptor);
            if ($url === false || $url[0] !== DIRECTORY_SEPARATOR) {
                return null;
            }

            $path = $transformation->getTransformer()->getTarget()
                . str_replace('/', DIRECTORY_SEPARATOR, $url);
        }

        $finder = new Pathfinder();
        $destination = preg_replace_callback(
            '/{{([^}]+)}}/', // explicitly do not use the unicode modifier; this breaks windows
            function ($query) use ($descriptor, $finder) {
                // strip any surrounding \ or /
                $filepart = trim((string) current($finder->find($descriptor, $query[1])), '\\/');

                // make it windows proof
                if (extension_loaded('iconv')) {
                    $filepart = iconv('UTF-8', 'ASCII//TRANSLIT', $filepart);
                }

                return strpos($filepart, '/') !== false
                    ? implode('/', array_map('urlencode', explode('/', $filepart)))
                    : implode('\\', array_map('urlencode', explode('\\', $filepart)));
            },
            $path
        );

        // replace any \ with the directory separator to be compatible with the
        // current filesystem and allow the next file_exists to do its work
        $destination = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destination);

        // create directory if it does not exist yet
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0777, true);
        }

        return $destination;
    }
}
