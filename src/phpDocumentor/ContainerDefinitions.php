<?php
use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\CacheInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use phpDocumentor\Command\Helper\ConfigurationHelper;
use phpDocumentor\Command\Helper\LoggerHelper;
use phpDocumentor\Command\Phar\UpdateCommand;
use phpDocumentor\Command\Project\RunCommand;
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\Linker\Linker;
use phpDocumentor\Compiler\Pass\ElementsIndexBuilder;
use phpDocumentor\Compiler\Pass\ExampleTagsEnricher;
use phpDocumentor\Compiler\Pass\MarkerFromTagsExtractor;
use phpDocumentor\Compiler\Pass\NamespaceTreeBuilder;
use phpDocumentor\Compiler\Pass\PackageTreeBuilder;
use phpDocumentor\Compiler\Pass\ResolveInlineLinkAndSeeTags;
use phpDocumentor\Configuration;
use phpDocumentor\Configuration\Loader;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\PhpParserAssemblers;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Parser\Backend\Php;
use phpDocumentor\Parser\Command\Project\ParseCommand;
use phpDocumentor\Parser\Listeners\Cache as CacheListener;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Partials\Collection as PartialsCollection;
use phpDocumentor\Partials\Exception\MissingNameForPartialException;
use phpDocumentor\Partials\Partial;
use phpDocumentor\Plugin\Core\Descriptor\Validator\DefaultValidators;
use phpDocumentor\Plugin\Core\Transformer\Writer\Checkstyle;
use phpDocumentor\Plugin\Core\Transformer\Writer\Xml;
use phpDocumentor\Plugin\Twig\Writer\Twig;
use phpDocumentor\Transformer\Command\Project\TransformCommand;
use phpDocumentor\Transformer\Command\Template\ListCommand;
use phpDocumentor\Transformer\Router\ExternalRouter;
use phpDocumentor\Transformer\Router\Queue;
use phpDocumentor\Transformer\Router\StandardRouter;
use phpDocumentor\Transformer\Template\PathResolver;
use phpDocumentor\Transformer\Transformer;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\DefaultTranslator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\MetadataFactoryInterface;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\ValidatorInterface;
use phpDocumentor\Descriptor;

return [
    // -- Parameters
    'application.version' => function () {
        return strpos('@package_version@', '@') === 0
            ? trim(file_get_contents(__DIR__ . '/../../VERSION'))
            : '@package_version@';
    },
    'cache.directory' => sys_get_temp_dir(),
    'linker.substitutions' => [
        Descriptor\ProjectDescriptor::class => [ 'files' ],
        Descriptor\FileDescriptor::class    => [ 'tags', 'classes', 'interfaces', 'traits', 'functions', 'constants' ],
        Descriptor\ClassDescriptor::class   => [
            'tags',
            'parent',
            'interfaces',
            'constants',
            'properties',
            'methods',
            'usedTraits',
        ],
        Descriptor\InterfaceDescriptor::class       => [ 'tags', 'parent', 'constants', 'methods' ],
        Descriptor\TraitDescriptor::class           => [ 'tags', 'properties', 'methods', 'usedTraits' ],
        Descriptor\FunctionDescriptor::class        => ['tags', 'arguments'],
        Descriptor\MethodDescriptor::class          => ['tags', 'arguments'],
        Descriptor\ArgumentDescriptor::class        => ['types'],
        Descriptor\PropertyDescriptor::class        => ['tags', 'types'],
        Descriptor\ConstantDescriptor::class        => ['tags', 'types'],
        Descriptor\Tag\ParamDescriptor::class       => ['types'],
        Descriptor\Tag\ReturnDescriptor::class      => ['types'],
        Descriptor\Tag\SeeDescriptor::class         => ['reference'],
        Descriptor\Type\CollectionDescriptor::class => ['baseType', 'types', 'keyTypes'],
    ],
    'template.localDirectory'    => __DIR__ . '/../../data/templates',
    'template.composerDirectory' => __DIR__ . '/../../../templates',
    'template.directory'         => function (ContainerInterface $c) {
        if (file_exists($c->get('template.composerDirectory'))) {
            return $c->get('template.composerDirectory');
        }

        return $c->get('template.localDirectory');
    },
    'config.template.path' => __DIR__ . '/Configuration/Resources/phpdoc.tpl.xml',
    'config.user.path'     => getcwd() . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml'),

    // -- Services
    Configuration::class => function (ContainerInterface $c) {
        /** @var Loader $loader */
        $loader = $c->get(Loader::class);

        return $loader->load($c->get('config.template.path'), $c->get('config.user.path'));
    },

    // Console
    Application::class => function (ContainerInterface $c) {
        $application = new Application('phpDocumentor', $c->get('application.version'));

        $application->getDefinition()->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Location of a custom configuration file'
            )
        );

        $application->getHelperSet()->set($c->get(LoggerHelper::class));
        $application->getHelperSet()->set($c->get(ConfigurationHelper::class));

        $application->add($c->get(ParseCommand::class));
        $application->add($c->get(RunCommand::class));
        $application->add($c->get(TransformCommand::class));
        $application->add($c->get(ListCommand::class));
        if (\Phar::running()) {
            $application->add($c->get(UpdateCommand::class));
        }

        return $application;
    },

    // Dispatcher
    Dispatcher::class => function () {
        return Dispatcher::getInstance();
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
    ValidatorInterface::class => \DI\object(Validator::class),
    MetadataFactoryInterface::class => \DI\object(LazyLoadingMetadataFactory::class)
        ->constructorParameter('loader', \DI\object(StaticMethodLoader::class)),
    ConstraintValidatorFactoryInterface::class => \DI\object(ConstraintValidatorFactory::class),
    TranslatorInterface::class => \DI\object(DefaultTranslator::class),

    // Descriptors
    InitializerChain::class => \DI\object()
        ->method('addInitializer', \DI\get(DefaultFilters::class))
        ->method('addInitializer', \DI\get(PhpParserAssemblers::class))
        ->method('addInitializer', \DI\get(ReflectionAssemblers::class))
        ->method('addInitializer', \DI\get(DefaultValidators::class)),

    // Cache
    AdapterInterface::class => \DI\Object(File::class)->constructor(\DI\get('cache.directory')),
    CacheInterface::class => \DI\object(Cache::class),

    // Parser
    Php::class => \DI\object()
        ->method('setEventDispatcher', \DI\get(Dispatcher::class)),
    CacheListener::class => \DI\object()
        ->method('register', \DI\get(Dispatcher::class)),
    Parser::class => \DI\object()
        ->method('registerEventDispatcher', \DI\get(Dispatcher::class))
        ->method('registerBackend', \DI\get(Php::class)),

    // Partials
    PartialsCollection::class => function (ContainerInterface $c) {
        $partialsCollection = new PartialsCollection($c->get(\Parsedown::class));

        /** @var Configuration $config */
        $config = $c->get(Configuration::class);

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
    },

    // Transformer
    Linker::class => \DI\object()->constructorParameter('substitutions', \DI\get('linker.substitutions')),
    Compiler::class => \DI\object()
        ->method('insert', \DI\get(ElementsIndexBuilder::class), ElementsIndexBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(MarkerFromTagsExtractor::class), MarkerFromTagsExtractor::COMPILER_PRIORITY)
        ->method('insert', \DI\get(ExampleTagsEnricher::class), ExampleTagsEnricher::COMPILER_PRIORITY)
        ->method('insert', \DI\get(PackageTreeBuilder::class), PackageTreeBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(NamespaceTreeBuilder::class), NamespaceTreeBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(ResolveInlineLinkAndSeeTags::class), ResolveInlineLinkAndSeeTags::COMPILER_PRIORITY)
        ->method('insert', \DI\get(Linker::class), Linker::COMPILER_PRIORITY)
        ->method('insert', \DI\get(Transformer::class), Transformer::COMPILER_PRIORITY),

    Queue::class => \DI\object()
        ->method('insert', \DI\get(ExternalRouter::class), 10500)
        ->method('insert', \DI\get(StandardRouter::class), 10000),

    // Templates
    PathResolver::class => \DI\object()
        ->constructorParameter('templatePath', \DI\get('template.directory')),

    Xml::class => \DI\object()->constructorParameter('router', \DI\get(StandardRouter::class))
];
