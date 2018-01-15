<?php

declare (strict_types=1);

namespace Pimple;

use phpDocumentor\Application\Console\Command\Command;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

if (!class_exists(\Pimple\Container::class, false)) {
    class Container implements \ArrayAccess
    {
        /** @var ContainerInterface */
        protected $container;

        public function __construct(ContainerInterface $container)
        {
            $this->container = $container;
        }

        public function offsetExists($offset)
        {
            return $this->container->has($offset);
        }

        public function offsetGet($offset)
        {
            try {
                return $this->container->get($offset);
            } catch (NotFoundExceptionInterface $exception) {
                return null;
            }
        }

        public function offsetSet($offset, $value)
        {
            if ($value instanceof \Closure) {
                $value = $value($this);
            }
            $this->container->set($offset, $value);
        }

        public function offsetUnset($offset)
        {
            $this->container->set($offset, null);
        }

        public function register(ServiceProviderInterface $serviceProvider)
        {
            $serviceProvider->register($this);
        }

        public function extend(string $serviceId, \Closure $extendingService)
        {
            return $extendingService($this->container->get($serviceId));
        }

        public function command(Command $command)
        {
            if ( ! $this->container->has('phpdocumentor.compatibility.extra_commands')) {
                $this->container->set('phpdocumentor.compatibility.extra_commands', new \ArrayObject());
            }

            $this->container->get('phpdocumentor.compatibility.extra_commands')->append($command);
        }
    }
}
