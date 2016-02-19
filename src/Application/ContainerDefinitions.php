<?php
use Interop\Container\ContainerInterface;
use League\Event\Emitter;
use League\Event\EmitterInterface;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\InvokeInflector;
use League\Tactician\Handler\MethodNameInflector\MethodNameInflector;
use phpDocumentor\Application\Parser\Documentation\Api\FromReflectionFactory;
use phpDocumentor\Application\Renderer\TwigRenderer;
use phpDocumentor\DomainModel\Dsn;
use phpDocumentor\DomainModel\Parser\Documentation\Api;
use phpDocumentor\Application\Console\Command\ListCommand;
use phpDocumentor\DomainModel\ReadModel\Mapper\Factory as MapperFactory;
use phpDocumentor\DomainModel\Renderer\Assets;
use phpDocumentor\Infrastructure\Renderer\FlySystemAssets;
use phpDocumentor\Infrastructure\Tactician\ContainerLocator;
use phpDocumentor\Application\Console\Command\Phar\UpdateCommand;
use phpDocumentor\Application\Console\Command\RunCommand;
use phpDocumentor\Application\Configuration\ConfigurationFactory;
use phpDocumentor\Application\Configuration\Factory\CommandlineOptionsMiddleware;
use phpDocumentor\Application\Configuration\Factory\PhpDocumentor2;
use phpDocumentor\Application\Configuration\Factory\PhpDocumentor3;
use phpDocumentor\DomainModel\Parser\Documentation\DocumentGroup\DocumentGroupFormat;
use phpDocumentor\DomainModel\Parser\DocumentationFactory;
use phpDocumentor\Infrastructure\Parser\StashDocumentationRepository;
use phpDocumentor\Infrastructure\FileSystemFactory;
use phpDocumentor\Infrastructure\FlySystemFactory;
use phpDocumentor\Infrastructure\Parser\SpecificationFactory as FlySystemSpecificationFactory;
use phpDocumentor\DomainModel\Parser\Version\DefinitionFactory;
use phpDocumentor\DomainModel\Parser\Version\DefinitionRepository;
use phpDocumentor\Reflection\ProjectFactory as ProjectFactoryInterface;
use phpDocumentor\Infrastructure\Reflection\Middleware\LoggingMiddleware;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Php\Factory\Argument;
use phpDocumentor\Reflection\Php\Factory\Class_;
use phpDocumentor\Reflection\Php\Factory\Constant;
use phpDocumentor\Reflection\Php\Factory\DocBlock;
use phpDocumentor\Reflection\Php\Factory\File;
use phpDocumentor\Infrastructure\Reflection\Middleware\CacheMiddleware;
use phpDocumentor\Reflection\Php\Factory\Function_;
use phpDocumentor\Reflection\Php\Factory\Interface_;
use phpDocumentor\Reflection\Php\Factory\Method;
use phpDocumentor\Reflection\Php\Factory\Property;
use phpDocumentor\Reflection\Php\Factory\Trait_;
use phpDocumentor\Reflection\Php\ProjectFactory;
use phpDocumentor\Application\Renderer\Template\Action\TwigHandler;
use phpDocumentor\Application\Renderer\Template\Action\XmlHandler;
use phpDocumentor\Application\Renderer\Template\Action\XslHandler;
use phpDocumentor\Application\Renderer\XsltRenderer\Extension;
use phpDocumentor\Infrastructure\Renderer\Template\LocalPathsRepository;
use phpDocumentor\DomainModel\Renderer\TemplateFactory;
use phpDocumentor\Infrastructure\Renderer\XmlTemplateFactory;
use phpDocumentor\DomainModel\Renderer\Template\PathsRepository;
use phpDocumentor\DomainModel\Renderer\Router\Queue;
use phpDocumentor\Application\Renderer\Router\StandardRouter;
use phpDocumentor\Infrastructure\SpecificationFactory;
use phpDocumentor\DomainModel\Uri;
use phpDocumentor\Application\ReadModel;
use phpDocumentor\Application\ReadModel\FromContainerFactory;
use phpDocumentor\Application\ReadModel\Mappers\Project;
use Stash\Driver\FileSystem;
use Stash\Pool;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\StaticMethodLoader;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    'template.localDirectory'    => __DIR__ . '/../../../data/templates',
    'template.composerDirectory' => __DIR__ . '/../../../../templates',
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
    'config.user.path'     => new Uri(getcwd()
        . ((file_exists(getcwd() . '/phpdoc.xml')) ? '/phpdoc.xml' : '/phpdoc.dist.xml')),
    'config.schema.path'   => __DIR__ . '/data/xsd/phpdoc.xsd',
    'config.strategies'    => [ \DI\get(PhpDocumentor3::class), \DI\get(PhpDocumentor2::class) ],
    'config.middlewares'   => [ \DI\get(CommandlineOptionsMiddleware::class) ],
    'twig.cache.path'      => sys_get_temp_dir() . '/phpdoc-twig-cache',

    // -- Services
    ContainerInterface::class => function (ContainerInterface $c) {
        return $c;
    },
    EmitterInterface::class => \DI\object(Emitter::class),

    // Command Bus
    CommandBus::class           => \DI\object()->constructor(\DI\get('command.middlewares')),
    CommandNameExtractor::class => \DI\object(ClassNameExtractor::class),
    HandlerLocator::class       => \DI\object(ContainerLocator::class),
    MethodNameInflector::class  => \DI\object(InvokeInflector::class),

    // Configuration
    ConfigurationFactory::class => \DI\object()
        ->constructorParameter('strategies', \DI\get('config.strategies'))
        ->constructorParameter('uri', \DI\get('config.user.path'))
        ->constructorParameter('middlewares', \DI\get('config.middlewares')),

    PhpDocumentor3::class => \DI\object()->constructorParameter('schemaPath', \DI\get('config.schema.path')),

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

    RunCommand::class => \DI\object()
        ->constructorParameter('documentationRepository', \DI\get(StashDocumentationRepository::class)),

    // Validator
    ValidatorInterface::class => \DI\object(RecursiveValidator::class),
    LazyLoadingMetadataFactory::class => \DI\object(LazyLoadingMetadataFactory::class)
        ->constructorParameter('loader', \DI\object(StaticMethodLoader::class)),
    ConstraintValidatorFactoryInterface::class => \DI\object(ConstraintValidatorFactory::class),
    TranslatorInterface::class => \DI\object(IdentityTranslator::class),

    //Definition Factories
    DefinitionFactory::class => function (ContainerInterface $c) {
        $factory = new \phpDocumentor\DomainModel\Parser\Version\DefinitionFactory();

        $factory->registerDocumentGroupDefinitionFactory(
            'api',
            new DocumentGroupFormat('php'),
            $c->get(Api\DocumentGroupDefinitionFactory::class)
        );

        return $factory;
    },
    DefinitionRepository::class => \DI\object(DefinitionRepository::class),

    // Documentation Repositories
    StashDocumentationRepository::class => \DI\object(StashDocumentationRepository::class),
    DocumentationFactory::class => \DI\object()
        ->method('addDocumentGroupFactory', \DI\get(FromReflectionFactory::class)),

    //ApiReference
    ProjectFactoryInterface::class => function (ContainerInterface $c) {
        $strategies = [
            $c->get(Argument::class),
            $c->get(Class_::class),
            $c->get(Constant::class),
            $c->get(DocBlock::class),
            $c->get(Function_::class),
            $c->get(Interface_::class),
            $c->get(Method::class),
            $c->get(Property::class),
            $c->get(Trait_::class),
            $c->get(File::class),
        ];

        return new ProjectFactory($strategies);
    },

    File::class => function (ContainerInterface $c) {
        $middleware = [
            $c->get(LoggingMiddleware::class),
            $c->get(CacheMiddleware::class),
        ];


        return new File(\phpDocumentor\Reflection\Php\NodesFactory::createInstance(), $middleware);
    },

    DocBlockFactoryInterface::class => function (ContainerInterface $c) {
        return DocBlockFactory::createInstance();
    },

    // Infrastructure
    Pool::class => function (ContainerInterface $c) {
        $adapter = new \Stash\Driver\FileSystem();
        $adapter->setOptions(['path' => $c->get('cache.directory')]);
        return new Pool($adapter);
    },

    FileSystemFactory::class => \DI\object(FlySystemFactory::class),
    SpecificationFactory::class => \DI\object(FlySystemSpecificationFactory::class),

    // Parser
    Queue::class => \DI\object()
// TODO: Refactor the external router to use the new configuration
//        ->method('insert', \DI\get(ExternalRouter::class), 10500)
        ->method('insert', \DI\get(StandardRouter::class), 10000),

    // Views
    MapperFactory::class => \DI\object(FromContainerFactory::class)
        ->constructorParameter('mapperAliases', [
            'php' => Project::class
        ]),

    // Renderer
    Assets::class => \DI\object(FlySystemAssets::class)
        ->constructorParameter('filesystem', \DI\factory(function (ContainerInterface $c) {
            $filesystemFactory = $c->get(FileSystemFactory::class);
            return $filesystemFactory->create(new Dsn('file://' . __DIR__ . '/../../data/templates'));
        })),

    // Templates
    LocalPathsRepository::class => \DI\object()
        ->constructorParameter('templateFolders', \DI\get('template.directories')),
    PathsRepository::class => \DI\object(LocalPathsRepository::class),
    XmlHandler::class => \DI\object()->constructorParameter('router', \DI\get(StandardRouter::class)),
    XslHandler::class => \DI\object()->constructorParameter('router', \DI\get(StandardRouter::class)),
    TemplateFactory::class => \DI\object(XmlTemplateFactory::class)
        ->constructorParameter('templateFolders', \DI\get('template.directories')),
    TwigRenderer::class => \DI\object()->constructorParameter('cacheFolder', \DI\get('twig.cache.path')),
    TwigHandler::class => \DI\object()->constructorParameter('renderer', \DI\get(TwigRenderer::class)),
];
