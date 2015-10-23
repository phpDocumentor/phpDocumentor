<?php
use Desarrolla2\Cache\Adapter\AdapterInterface;
use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\CacheInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Interop\Container\ContainerInterface;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use phpDocumentor\Application\Cli\Command\ListCommand;
use phpDocumentor\Application\CommandBus\ContainerLocator;
use phpDocumentor\Application\Cli\Command\Phar\UpdateCommand;
use phpDocumentor\Application\Cli\Command\RunCommand;
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
use phpDocumentor\ConfigurationFactory;
use phpDocumentor\ConfigurationFactory\PhpDocumentor2;
use phpDocumentor\ConfigurationFactory\PhpDocumentor3;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerChain;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\DefaultFilters;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\PhpParserAssemblers;
use phpDocumentor\Descriptor\ProjectDescriptor\InitializerCommand\ReflectionAssemblers;
use phpDocumentor\DocumentationFactory;
use phpDocumentor\DocumentationRepository;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\FileSystemFactory;
use phpDocumentor\FlySystemFactory;
use phpDocumentor\Infrastructure\FlyFinder\SpecificationFactory as FlySystemSpecificationFactory;
use phpDocumentor\Parser\Backend\Php;
use phpDocumentor\Parser\Listeners\Cache as CacheListener;
use phpDocumentor\Parser\Parser;
use phpDocumentor\Plugin\Core\Descriptor\Validator\DefaultValidators;
use phpDocumentor\Project\Version\DefinitionFactory;
use phpDocumentor\Project\Version\DefinitionRepository;
use phpDocumentor\Renderer\Action\TwigHandler;
use phpDocumentor\Renderer\Action\XmlHandler;
use phpDocumentor\Renderer\Action\XslHandler;
use phpDocumentor\Renderer\Action\Xslt\Extension;
use phpDocumentor\Renderer\Template\PathsRepository;
use phpDocumentor\Renderer\TemplateFactory;
use phpDocumentor\Renderer\Router\ExternalRouter;
use phpDocumentor\Renderer\Router\Queue;
use phpDocumentor\Renderer\Router\StandardRouter;
use phpDocumentor\SpecificationFactory;
use phpDocumentor\Uri;
use phpDocumentor\Views\MapperFactory;
use phpDocumentor\Views\MapperFactory\Container;
use phpDocumentor\Views\Mappers\Project;
use Stash\Driver\FileSystem;
use Stash\Pool;
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

// maintain BC in XSL-based templates
if (!class_exists('phpDocumentor\\Plugin\\Core\\Xslt\\Extension')) {
    class_alias(Extension::class, 'phpDocumentor\\Plugin\\Core\\Xslt\\Extension');
}

return [
    // -- Parameters
    'application.version' => function () {
        return strpos('@package_version@', '@') === 0
            ? trim(file_get_contents(__DIR__ . '/../../VERSION'))
            : '@package_version@';
    },
    'command.middlewares' => [
        \DI\get(CommandHandlerMiddleware::class)
    ],
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
    'template.directories' => [
        __DIR__ . '/../../data',
        __DIR__ . '/../../data/templates',
    ],
    'config.template.path' => __DIR__ . '/Configuration/Resources/phpdoc.tpl.xml',
    'config.user.path'     => getcwd() . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml'),
    'twig.cache.path'      => sys_get_temp_dir() . '/phpdoc-twig-cache',

    // -- Services
    ContainerInterface::class => function (ContainerInterface $c) {
        return $c;
    },

    // Command Bus
    CommandBus::class           => \DI\object()->constructor(\DI\get('command.middlewares')),
    CommandNameExtractor::class => \DI\object(ClassNameExtractor::class),
    HandlerLocator::class       => \DI\object(ContainerLocator::class),
    MethodNameInflector::class  => \DI\object(InvokeInflector::class),

    // Configuration
    ConfigurationFactory::class => function (ContainerInterface $c) {
        return new \phpDocumentor\ConfigurationFactory([
            new PhpDocumentor3(''),
            new PhpDocumentor2(),
        ],
        new Uri($c->get('config.user.path')));
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

        $application->add($c->get(RunCommand::class));
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

    // Validator
    ValidatorInterface::class => \DI\object(Validator::class),
    MetadataFactoryInterface::class => \DI\object(LazyLoadingMetadataFactory::class)
        ->constructorParameter('loader', \DI\object(StaticMethodLoader::class)),
    ConstraintValidatorFactoryInterface::class => \DI\object(ConstraintValidatorFactory::class),
    TranslatorInterface::class => \DI\object(DefaultTranslator::class),

    //Definition Factories
    \phpDocumentor\ApiReference\DocumentGroupDefinitionFactory::class => \DI\object(\phpDocumentor\ApiReference\DocumentGroupDefinitionFactory::class),
    DefinitionFactory::class => function (ContainerInterface $c) {
        $factory = new \phpDocumentor\Project\Version\DefinitionFactory();

        $factory->registerDocumentGroupDefinitionFactory(
            'api',
            new \phpDocumentor\DocumentGroupFormat('php'),
            $c->get(\phpDocumentor\ApiReference\DocumentGroupDefinitionFactory::class)
        );

        return $factory;
    },
    DefinitionRepository::class => \Di\object(DefinitionRepository::class),

    //Documentation Respositories
    DocumentationRepository::class => \DI\object(DocumentationRepository::class),
    DocumentationFactory::class => \DI\object()
        ->method('addDocumentGroupFactory', \DI\get(phpDocumentor\ApiReference\Factory::class)),

    // Descriptors
    InitializerChain::class => \DI\object()
        ->method('addInitializer', \DI\get(DefaultFilters::class))
        ->method('addInitializer', \DI\get(PhpParserAssemblers::class))
        ->method('addInitializer', \DI\get(ReflectionAssemblers::class))
        ->method('addInitializer', \DI\get(DefaultValidators::class)),

    // Infrastructure
    Pool::class => function (ContainerInterface $c) {
        $adapter = new \Stash\Driver\BlackHole();
        $adapter->setOptions(['path' => $c->get('cache.directory')]);
        return new Pool($adapter);
    },

    FileSystemFactory::class => \DI\object(FlySystemFactory::class),
    SpecificationFactory::class => \DI\object(FlySystemSpecificationFactory::class),

    // Parser
    Php::class => \DI\object()
        ->method('setEventDispatcher', \DI\get(Dispatcher::class)),
    CacheListener::class => \DI\object()
        ->method('register', \DI\get(Dispatcher::class)),
    Parser::class => \DI\object()
        ->method('registerEventDispatcher', \DI\get(Dispatcher::class))
        ->method('registerBackend', \DI\get(Php::class)),

    // Compiler
    Linker::class => \DI\object()->constructorParameter('substitutions', \DI\get('linker.substitutions')),
    Compiler::class => \DI\object()
        ->method('insert', \DI\get(ElementsIndexBuilder::class), ElementsIndexBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(MarkerFromTagsExtractor::class), MarkerFromTagsExtractor::COMPILER_PRIORITY)
        ->method('insert', \DI\get(ExampleTagsEnricher::class), ExampleTagsEnricher::COMPILER_PRIORITY)
        ->method('insert', \DI\get(PackageTreeBuilder::class), PackageTreeBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(NamespaceTreeBuilder::class), NamespaceTreeBuilder::COMPILER_PRIORITY)
        ->method('insert', \DI\get(ResolveInlineLinkAndSeeTags::class), ResolveInlineLinkAndSeeTags::COMPILER_PRIORITY)
        ->method('insert', \DI\get(Linker::class), Linker::COMPILER_PRIORITY),

    Queue::class => \DI\object()
        ->method('insert', \DI\get(ExternalRouter::class), 10500)
        ->method('insert', \DI\get(StandardRouter::class), 10000),

    // Views
    MapperFactory::class => \DI\object(Container::class)
        ->constructorParameter('mapperAliases', [
            'php' => Project::class
        ]),

    // Templates
    PathsRepository::class => \DI\object()->constructorParameter('templateFolders', \DI\get('template.directories')),
    XmlHandler::class => \DI\object()->constructorParameter('router', \DI\get(StandardRouter::class)),
    XslHandler::class => \DI\object()->constructorParameter('router', \DI\get(StandardRouter::class)),
    TemplateFactory::class => \DI\object()
        ->constructorParameter('templateFolders', \DI\get('template.directories')),
    TwigHandler::class => \DI\object()->constructorParameter('cacheFolder', \DI\get('twig.cache.path')),
];
