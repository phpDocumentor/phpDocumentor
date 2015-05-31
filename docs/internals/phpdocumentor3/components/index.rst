Aggregates
----------

In order to better understand and guard how phpDocumentor works with its Domain Models we have identified several
AGGREGATES_. An AGGREGATE is a series of classes whose objects cannot exist without each other and where one class
is responsible for guarding the boundaries of the whole AGGREGATE; this class is called the AGGREGATE ROOT.

The following AGGREGATES are identified in phpDocumentor:

.. toctree::
   :max-depth: 2

   configuration
   version-definition
   documentation
   template

.. _AGGREGATES: http://martinfowler.com/bliki/DDD_Aggregate.html
