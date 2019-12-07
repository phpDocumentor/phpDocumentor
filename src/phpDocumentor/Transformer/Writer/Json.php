<?php declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ArgumentDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Transformation;

final class Json extends WriterAbstract
{
    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    protected function router() : ?Router
    {
        return $this->router;
    }

    /**
     * @inheritDoc
     */
    public function transform(ProjectDescriptor $project, Transformation $transformation) : void
    {
        foreach ($project->getFiles() as $file) {
            // TODO: Figure out how to better add the extension; the .html is defined in the routes.yaml
            $destination = $this->destination($file, $transformation) . '.json';

            $fileData = [
                'name' => (string) $file->getName(),
                'hash' => (string) $file->getHash(),
                'path' => (string) $file->getPath(),
                'summary' => (string) $file->getSummary(),
                'description' => (string) $file->getDescription(),
                'classes' => array_map(
                    function (ClassDescriptor $class) {
                        return $this->mapClass($class);
                    },
                    $file->getClasses()->getAll()
                ),
            ];

            \file_put_contents($destination, \json_encode($fileData));
        }
    }

    private function mapClass(ClassDescriptor $class) : array
    {
        return [
            'name' => (string) $class->getName(),
            'namespace' => (string) $class->getNamespace(),
            'fqsen' => (string) $class->getFullyQualifiedStructuralElementName(),
            'summary' => (string) $class->getSummary(),
            'description' => (string) $class->getDescription(),
            'tags' => array_map(
                function (TagDescriptor $tag) {
                    return $this->mapTag($tag);
                },
                $class->getTags()->getAll()
            ),
            'methods' => array_map(
                function (MethodDescriptor $method) {
                    return $this->mapMethod($method);
                },
                $class->getMethods()->getAll()
            ),
        ];
    }

    private function mapMethod(MethodDescriptor $method) : array
    {
        return [
            'name' => (string) $method->getName(),
            'fqsen' => (string) $method->getFullyQualifiedStructuralElementName(),
            'summary' => (string) $method->getSummary(),
            'description' => (string) $method->getDescription(),
            'arguments' => array_map(
                function (ArgumentDescriptor $argument) {
                    return $this->mapArgument($argument);
                },
                $method->getArguments()->getAll()
            ),
            'returns' => [
                'description' => (string) $method->getResponse()->getDescription(),
                'type' => (string) $method->getResponse()->getType(),
            ],
        ];
    }

    private function mapArgument(ArgumentDescriptor $argument): array
    {
        return [
            'name' => $argument->getName(),
            'description' => $argument->getDescription(),
            'default' => $argument->getDefault(),
            'isVariadic' => $argument->isVariadic(),
            'byReference' => $argument->isByReference(),
            'type' => (string) $argument->getType(),
        ];
    }
}
