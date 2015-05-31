Ubiquitous Language Glossary
============================

phpDocumentor covers a variety of concepts and as such has its own UBIQUITOUS LANGUAGE. In this glossary are the most
important concepts in the UBIQUITOUS LANGUAGE explained.

Basic (DDD) Concepts
--------------------

phpDocumentor uses concepts of Domain Driven Design to assist in creating a recognizable architecture. In this list of
terms we repeat some of the concepts introduced by the Domain Driven Design school of thought. It is however recommended
to read up on the concept or read the book `Domain Driven Design by Eric Evans`_ to get a more thorough understanding of
the concepts used here.

Entity
    An ENTITY represents a real-world object that should be tracked or has an IDENTITY. An example of this, in the
    context of phpDocumentor, is the ``Documentation`` object: it needs to able to be retrieved and has a Version as
    IDENTITY.

Value Object
    A VALUE OBJECT is, similar to an ENTITY, a representation of a real-world concept or object but one that is not
    tracked and does not have an IDENTITY of its own. An example of this, in the context of phpDocumentor, is the
    Template class: A Template is loaded from a template definition file but once loaded it has no IDENTITY of its
    own and will function as a source to the Renderer.

Service

Factory
    A FACTORY is a Design Pattern that describes a SERVICE which creates new ENTITIES or VALUE OBJECTS if they
    require business logic with their instantiation. ENTITIES and VALUE OBJECTS without additional business rules
    upon instantiation generally do not have FACTORIES associated with them but are instantiated directly.

    The Wikipedia definition of a FACTORY is:

        > In object-oriented programming, a factory is an object for creating other objects – formally a factory is
        > simply an object that returns an object from some method call, which is assumed to be "new".[a] More broadly,
        > a subroutine that returns a "new" object may be referred to as a "factory", as in factory method or factory
        > function. This is a basic concept in OOP, and forms the basis for a number of related software design
        > patterns.

    In phpDocumentor several FACTORIES are in use, both FACTORY SERVICES (Objects that create VALUE OBJECTS and
    ENTITIES) as well as FACTORY METHODS (public static methods in a VALUE OBJECT or ENTITY that creates an object of
    that class).

    For more information on the FACTORY Design Pattern: please consult the Wikipedia page, the book Design Patterns by
    the Gang of Four or the book `Domain Driven Design by Eric Evans`_.

Repository
    A REPOSITORY is a Design Pattern that describes a SERVICE that is responsible for persisting and retrieving
    ENTITIES.

Application Concepts
--------------------

Dependency Injection Container (DIC)
    A Dependency Injection Container is a system that tracks all instantiated SERVICES and know how to instantiate new
    SERVICES that are based on those already instantiated SERVICES.

    The Wikipedia definition of Dependency Injection is:

        > Dependency injection is a software design pattern in which one or more dependencies (or services) are
        > injected, or passed by reference, into a dependent object (or client) and are made part of the client's
        > state. The pattern separates the creation of a client's dependencies from its own behavior, which allows
        > program designs to be loosely coupled and to follow the dependency inversion and single responsibility
        > principles. It directly contrasts with the service locator pattern, which allows clients to know about the
        > system they use to find dependencies.

    phpDocumentor uses PHP-DI_ as its Dependency Injection Container. This container supports auto-wiring and because of
    that it is not necessary to define all SERVICES (contrary to other containers). PHP-DI_ uses the type hints on
    constructor parameters to determine which SERVICES to fetch and inject.

    It is not possible for PHP-DI_ to determine which SERVICES to use as dependency if a constructor has an argument
    that is typed with an interface or an untyped parameter. In these cases PHP-DI needs to be told which dependencies
    or options to inject using a Module.

Module Definition
    Sometimes PHP-DI_ (the Dependency Injection Container, see above) is unable to determine which values or SERVICES
    to inject when constructing a new SERVICES. This happens, for example, when a constructor has untyped parameters or
    is typed using an interface.

    In cases like this PHP-DI_ needs to be helped a hand by using a Module Definition. This is a file that returns an
    array that maps SERVICES, or interfaces thereof, onto a SERVICE Definition. Please view the documentation on
    defining injections at the PHP-DI_ homepage for more information on how to declare these arrays:
    http://php-di.org/doc/definition.html.

Command

Command Handler

Command Bus

Domain Concepts
---------------

Definition

Documentation
~~~~~~~~~~~~~

Project
    A Project is a series of Documentation spanning multiple versions where each version refers to the same undertaking.
    Examples of a project can be a Software Project, such as phpDocumentor_, or the authoring of a Book or standalone
    reference, such as PSR-5_.

    phpDocumentor does not actively track a Project but instead assumes that all Versions that are defined in the
    configuration belong to the same project.

Version
    A Version of a Project represents a unique series of files that can be transformed into a set of Documentation. It
    is not necessary for the locations of Document Groups to be on the same Data Source as sometimes manuals and source
    files are located on different locations.

    > For example: the source files used to generate an Api Reference may be in a different VCS repository than the
    > source files for the User Manual for the same Version

Documentation

Document Group
    A set of Documentation can include several multiple Guides, Api References and similar objects. Such an object is
    called a Document Group as it combines into a logical grouping and offers the option to add new Document Groups
    in the future should the need arise.

Guides
~~~~~~

Guide

Document

Chapter

API Documentation
~~~~~~~~~~~~~~~~~

Api Reference

Fqsen

Element

PHP
+++

Visibility

Namespace

Constant

Function

Class

Interface

Trait

Class constant

Property

Method

DocBlock

Summary

Description

Tag

Rendering
~~~~~~~~~

View

Renderer

RenderAction

Template

.. _PHP-DI:                             http://www.php-di.org
.. _Domain Driven Design by Eric Evans: http://www.domaindrivendesign.org/books/evans_2003
.. _this blog post by Philip Brown:     http://culttt.com/2014/04/30/difference-entities-value-objects/
.. _Wikipedia page on Entities in DDD:  http://en.wikipedia.org/wiki/Domain-driven_design#Building_blocks_of_DDD
.. _phpDocumentor:                      http://www.phpdoc.org/
.. _PSR-5:                              https://github.com/phpDocumentor/fig-standards/blob/master/proposed/phpdoc.md
