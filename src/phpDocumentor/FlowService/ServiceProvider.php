<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;

/**
 * @template T of object
 */
final class ServiceProvider
{
    /**
     * @var array<string, T>
     */
    private $services;

    public function __construct(iterable $services)
    {
        foreach ($services as $key => $service) {
            $this->services[$key] = $service;
        }
    }

    /** @return T */
    public function get(DocumentationSetDescriptor $documetationSet): object
    {
        dump(get_class($documetationSet));
        if (isset($this->services[get_class($documetationSet)])) {
            return $this->services[get_class($documetationSet)];
        }

        throw new \Exception();
    }
}
