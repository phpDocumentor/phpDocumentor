Read Model
==========

The Read Model is the component that binds between the Parsing and Rendering side of the application. The design of
phpDocumentor is based on the CQRS theory where we have a WriteModel (represented by the Documentation aggregate) and
we can have one or more Read Models that provide a projection of the Documentation Aggregate.

With this layer of separation we are able to render templates from phpDocumentor 1, 2, 3 and other generating
applications because we can create a Read Model that matches the required structure for that application.

Another use of the Read Model is to augment the data from the Documentation Aggregate by, for example, building a
Read Model where methods and properties are inherited from their base classes.

Concepts
--------

ReadModel
    The ReadModel Aggregate offers the data that is aggregated from the Documentation Aggregate by a Mapper. The Mapper
    is responsible for the type of data that is contained in the ReadModel Aggregate, as such a ReadModel can return an
    object, array or scalar.

Mapper
    A Mapper is a service that is responsible for interpreting the Documentation Aggregate and generating a projection
    from the parsed project. A Mapper will afterwards apply any filters that are added onto the Mapper. The end result
    is a Read Model Aggregate that contains the data that was collected by the Mapper.

Filter
    A Filter is a re-usable component that can perform a transformation on the data that was collected by the Mapper.
    An example of a Filter can be that the collected data is iterated and every encountered FQCN object is replaced
    by an object reference if that object exists in the collected data.

Theory of Operation
-------------------

