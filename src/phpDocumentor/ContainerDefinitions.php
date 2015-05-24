<?php
use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\CacheInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use phpDocumentor\Configuration;
use phpDocumentor\Configuration\Loader;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\PhpParserAssemblers;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Backend\Php;
use phpDocumentor\Parser\Listeners\Cache as CacheListener;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Partials\Collection as PartialsCollection;
use phpDocumentor\Partials\Exception\MissingNameForPartialException;
use phpDocumentor\Partials\Partial;
use phpDocumentor\Plugin\Core\Descriptor\Validator\DefaultValidators;
use phpDocumentor\Translator\Translator;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ValidatorInterface;
use Zend\I18n\Translator\TranslatorInterface as ZendTranslatorInterface;

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
        $translator->addTranslationFolder(__DIR__ . '/Parser/Messages');

        return $translator;
    },
    ZendTranslatorInterface::class => \DI\link(Translator::class),

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
    ValidatorInterface::class => \DI\object(Validator::class),
    MetadataFactoryInterface::class => \DI\object(LazyLoadingMetadataFactory::class)
        ->constructorParameter('loader', \DI\object(StaticMethodLoader::class)),
    ConstraintValidatorFactoryInterface::class => \DI\object(ConstraintValidatorFactory::class),
    TranslatorInterface::class => \DI\object(DefaultTranslator::class),

    // Descriptors
    InitializerChain::class => \DI\object()
        ->method('addInitializer', \DI\link(DefaultFilters::class))
        ->method('addInitializer', \DI\link(PhpParserAssemblers::class))
        ->method('addInitializer', \DI\link(ReflectionAssemblers::class))
        ->method('addInitializer', \DI\link(DefaultValidators::class)),

    CacheInterface::class => function () {
        $adapter = new File(sys_get_temp_dir());
        $cache = new Cache($adapter);

        return $cache;
    },

    Php::class => \DI\object()
        ->method('setEventDispatcher', \DI\link(Dispatcher::class)),
    CacheListener::class => \DI\object()
        ->method('register', \DI\link(Dispatcher::class)),
    Parser::class => \DI\object()
        ->method('registerEventDispatcher', \DI\link(Dispatcher::class))
        ->method('registerBackend', \DI\link(Php::class)),

    PartialsCollection::class => function (ContainerInterface $c) {
        $partialsCollection = new PartialsCollection($c->get(\Parsedown::class));

        /** @var Configuration $config */
        $config = $c->get('config');

        // TODO: Move to factory!

        /** @var Partial[] $partials */
        $partials = $config->getPartials();
        if ($partials) {
            foreach ($partials as $partial) {
                if (! $partial->getName()) {
                    throw new MissingNameForPartialException('The name of the partial to load is missing');
                }

                $content = '';
                if ($partial->getContent()) {
                    $content = $partial->getContent();
                } elseif ($partial->getLink()) {
                    if (! is_readable($partial->getLink())) {
                        continue;
                    }

                    $content = file_get_contents($partial->getLink());
                }
                $partialsCollection->set($partial->getName(), $content);
            }
        }

        return $partialsCollection;
    }
];
