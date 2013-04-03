Serialization
=============

Abstract
--------

The goal of this research is to determine whether it is feasable to have the full Syntax Tree in memory using objects
and to be able to serialize and deserialize those objects within a given set of `parameters`_.

The outcome of this research should be whether the object model can be stored in memory and which serialization methods
perform best.

Introduction
------------

phpDocumentor currently parses aggregated data from PHP source files and exports that directly to XML; where the
transformer reads the XML, alters the state and feeds this directly to the XSL Processor. This approach had been
chosen for it's speed, straight-forward implementation and to reduce memory usage but increase speed.

During development it is noticed that this approach has several disadvantages:

#. XSL is complicated to write and thus not suited for all audiences
#. XSL relies on a PHP extension that is not always available
#. Inheritance is implemented by copying data from parent elements to child elements, which is slow
   and memory intensive.
#. Processing the XML document has memory usage in the libXML library that is not detected by PHP and thus takes more
   than originally intended.
#. Impossible to use a different file type or back end for the AST because the transformation process relies on XML.
#. Transformation writers that rely on an object model (such as Twig) can have trouble navigating using
   SimpleXMLElement objects.

It is expected that by using a normal object model instead of relying on the SimpleXMLElement classes and direct
modification of the XML will result in a faster, more flexible system.

Benchmark subjects
------------------

This research will use other Open Source projects as subjects in order to determine effectiveness of this approach.

The following projects have been selected:

* `Zend Framework 2 <https://github.com/zendframework/zf2>`_, with 249,505 lines of code and 1472 classes is this
   library a perfect fit to represent a **large** project.
* `Magento 2 <https://github.com/magento/magento2>`_, with 1,348,396 lines of code and 7573 classes is this application
  a good example of a **huge** project.

Important to note is that this research does not describe or compare the qualities of aforementioned projects, they
serve merely to benchmark the creation of an object model and to measure the effectiveness of several serialization
methods.

.. note::

   Originally there were several more benchmark subjects planned, due to similar results between Zend Framework 2 and
   Magento 2 these have been cancelled; given the nature of the results of this research the conclusion was clear.

Methodology
-----------

The approach to finding an answer to this problem consists of the following actions:

1. Find different methods of serialization to compare.
2. Populate a representative data set.
3. Attempt to serialize the data set and register the following characteristics:

   1. Amount of time in seconds until serialization is completed.
   2. Amount of time in seconds until deserialization is completed.
   3. Number of megabytes memory usage at the peak of execution.
   4. Size of the resulting artifact in kilobytes.

4. Populate a data set known to be exceptionally large.
5. For each method of serialization; attempt to serialize the second data set and register the characteristics
   mentioned in step 3.
6. Aggregate the results and determine the conclusion.

Results
-------

Zend Framework 2
~~~~~~~~~~~~~~~~

**Serialization**

=================== ====== ====== ====== ====== ====== ====== ========= ================
Type                Pass 1 Pass 2 Pass 3 Pass 4 Pass 5 Mean   File size Peak memory used
=================== ====== ====== ====== ====== ====== ====== ========= ================
serialize           1.0004 0.9158 0.8801 0.9901 0.9609 0.9494 14.0mb    +60mb
json_encode         0.2663 0.4634 0.2790 0.2588 0.3098 0.3155  8.8mb    +16mb
igbinary_serialize  0.2253 0.2107 0.2038 0.2875 0.1979 0.2250  3.3mb    + 7mb
=================== ====== ====== ====== ====== ====== ====== ========= ================

**Deserialization**

==================== ====== ====== ====== ====== ====== ======
Type                 Pass 1 Pass 2 Pass 3 Pass 4 Pass 5 Mean
==================== ====== ====== ====== ====== ====== ======
deserialize          2.6614 1.5656 1.7147 2.5170 1.5732 2.006
json_decode          0.6404 0.5388 0.7459 0.5162 0.4181 0.5719
igbinary_deserialize 0.3245 0.2466 0.3035 0.1993 0.1924 0.2533
==================== ====== ====== ====== ====== ====== ======

.. note::

   json_decode does not know how to transform classes back to their original types
   because json by nature does not store such information. This will decrease the value
   of json_decode as type-retention is desirable in the context of this application.

Magento 2
~~~~~~~~~

**Serialization**

=================== ====== ====== ====== ====== ====== ====== ========= ================
Type                Pass 1 Pass 2 Pass 3 Pass 4 Pass 5 Mean   File size Peak memory used
=================== ====== ====== ====== ====== ====== ====== ========= ================
serialize           4.0912 3.1885 3.8392 3.3655 3.1090 3.5187 55mb      +269mb
json_encode         1.0389 1.6685 2.3115 1.0769 3.1391 1.8470 45mb      +78mb
igbinary_serialize  2.0011 0.8306 1.2812 2.1513 1.6902 1.5909 14mb      +31mb
=================== ====== ====== ====== ====== ====== ====== ========= ================

**Deserialization**

==================== ======= ======= ======= ======= ======= =======
Type                 Pass 1  Pass 2  Pass 3  Pass 4  Pass 5  Mean
==================== ======= ======= ======= ======= ======= =======
deserialize          27.9888 27.0000 29.0000 29.0000 27.1046 28.0187
json_decode           1.9304  4.6521  2.6136  1.4787  1.4096  2.4168
igbinary_deserialize  2.3876  2.5162  1.3756  1.5820  0.9632  1.7649
==================== ======= ======= ======= ======= ======= =======

.. note::

   json_decode does not know how to transform classes back to their original types
   because json by nature does not store such information. This will decrease the value
   of json_decode as type-retention is desirable in the context of this application.

Conclusion
----------

Based on this research the following conclusions could be made based on the previous results and the following
corollary,

    ZF2 is 18.5% LOC of Magento 2 (thus Magento 2 is 5.5 times the size of ZF2)

    Serialize's speed on ZF2 was 27.0% of Magento 2  (less than 4-fold increase)
    JsonEncode's speed on ZF2 was 17.1% of Magento 2 (almost 6-fold increase)
    IGB's speed on ZF2 was 14.1% of Magento 2 (seven-fold increase)

    Memory size for all methods on ZF2 was approx. 20% of Magento 2 (5 fold increase)

Igbinary outperforms serialize by far both in terms of memory and speed, where JSON Encode follows closely as second.
It can be seen that igbinary becomes less efficient if the dataset is larger but given igbinary's inherent efficiency
does this scale sufficiently for the purposes of phpDocumentor.

A large downside for json_encode and json_decode is that it does not remember which class was associated with a
serialized object. For phpDocumentor this is important because this takes additional effort when rebuilding the object
model.

Another conclusion is that it is feasable for phpDocumentor to hold the object model of a large and even huge scale
application, Zend Framework's object model using this POC was 111mb and Magento's was 498mb. This also shows that it is
with reasonable certainty that we can determine that the used memory for a model rises just as fast percentually as the
amount of LOC in large to huge projects.

.. _`PHP's serialize`:           http://php.net/manual/en/language.oop5.serialization.php
.. _`json_encode & json_decode`: http://php.net/manual/en/ref.json.php
.. _`Igbinary`:                  http://github.com/phadej/igbinary
