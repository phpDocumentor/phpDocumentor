<?php

declare (strict_types=1);

namespace Pimple;

use phpDocumentor\Command\Command;
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
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            return $this->container->has($offset);
        }

        public function offsetGet($offset)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            try {
                return $this->container->get($offset);
            } catch (NotFoundExceptionInterface $exception) {
                return null;
            }
        }

        public function offsetSet($offset, $value)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            if ($value instanceof \Closure) {
                $value = $value($this);
            }
            $this->container->set($offset, $value);
        }

        public function offsetUnset($offset)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            $this->container->set($offset, null);
        }

        public function register(ServiceProviderInterface $serviceProvider)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            $serviceProvider->register($this);
        }

        public function extend(string $serviceId, \Closure $extendingService)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );

            return $extendingService($this->container->get($serviceId));
        }

        public function command(Command $command)
        {
            trigger_error(
                'Usage of pimple container is deprecated and will be remove in v4,
                 please use the symfony service wiring.',
                E_USER_DEPRECATED
            );


            if ( ! $this->container->has('phpdocumentor.compatibility.extra_commands')) {
                $this->container->set('phpdocumentor.compatibility.extra_commands', new \ArrayObject());
            }

            $this->container->get('phpdocumentor.compatibility.extra_commands')->append($command);
        }
    }
}
