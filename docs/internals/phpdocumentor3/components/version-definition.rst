Version Definition
------------------

.. image:: diagrams/version-definition.png

The Version Definition AGGREGATE contains several Entities that work together to declare a definition for a new or
existing Documentation object.

A Definition can be seen as a specification that informs a Factory how to construct a new object. In the current
design we plan on having a Version Definition that is used as basis for creating a new Documentation object using the
Documentation Factory; and to have a Definition for each type of DocumentGroup.

The above means that we need a common interface for Definitions for DocumentGroups but the actual Definition classes
are part of their respective DocumentGroup component and as such not documented as part of this component.

With the DefinitionRepository for the Versions it is possible to retrieve a Definition for each Version that is
defined in the configuration file. As can be seen in the Class Diagram it accepts the ConfigurationFactory, which will
create an array containing configuration settings upon calling its ‘get’ method. The ConfigurationFactory allows the
Configuration to be retrieved by the DefinitionRepository as a Data source but postpone loading the configuration until
absolutely necessary so that a user can provide their own Configuration file location.

Since each DocumentGroup can have a separate set of properties (a Guide using RestructuredText as Format can have
different properties from an ApiReference using PHP as Format) each can have their own specialised Factory (part of
the Component for that specific DocumentGuide); since the Version Definition Factory needs to be able to know when to
use which Factory these specialized Factories need to be registered using the registerDocumentGroupDefinitionFactory
method.
