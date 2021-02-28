<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use Symfony\Component\DependencyInjection\Argument\ServiceLocator;

/**
 * @template T of FlowService
 */
final class ServiceProvider
{
    /**
     * @var iterable<T>
     */
    private $services;

    public function __construct(iterable $services)
    {
        foreach ($services as $key => $service) {
            $this->services[$key] = $service;
        }
    }

    /** @return T */
    public function get(DocumentationSetDescriptor $documetationSet): FlowService
    {
        if (isset($this->services[get_class($documetationSet)])) {
            return $this->services[get_class($documetationSet)];
        }

        throw new \Exception();
    }
}
