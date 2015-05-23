<?php
use Doctrine\Common\Annotations\AnnotationRegistry;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use phpDocumentor\Configuration\Loader;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Translator\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;

return [
    'config' => function (ContainerInterface $c) {
        /** @var Loader $loader */
        $loader = $c->get(Loader::class);

        $configTemplate = __DIR__ . '/Configuration/Resources/phpdoc.tpl.xml';
        $userPath = getcwd() . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml');

        return $loader->load($configTemplate, $userPath);
    },

    // Dispatcher
    Dispatcher::class => function () {
        return Dispatcher::getInstance();
    },

    // Translation
    Translator::class => function (ContainerInterface $c) {
        $translator = new Translator();
        $translator->setLocale($c->get('config')->getTranslator()->getLocale());

        return $translator;
    },

    // Serializer
    Serializer::class => function (ContainerInterface $c) {
        $vendorPath     = $c->get('composer.vendor_path') ?: __DIR__ . '/../vendor';
        $serializerPath = $vendorPath . '/jms/serializer/src';

        AnnotationRegistry::registerAutoloadNamespace('JMS\Serializer\Annotation', $serializerPath);
        AnnotationRegistry::registerAutoloadNamespace(
            'phpDocumentor\Configuration\Merger\Annotation',
            __DIR__ . '/..'
        );

        return SerializerBuilder::create()->build();
    },

    // Validator
    MetadataFactoryInterface::class => \DI\object(LazyLoadingMetadataFactory::class)
        ->constructorParameter('loader', \DI\object(StaticMethodLoader::class)),
    ConstraintValidatorFactoryInterface::class => \DI\object(ConstraintValidatorFactory::class),
    TranslatorInterface::class => \DI\object(DefaultTranslator::class)
];
