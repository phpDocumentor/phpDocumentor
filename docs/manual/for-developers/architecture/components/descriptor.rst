Descriptor
==========

Builder
-------

The ProjectDescriptor, and all objects underneath it, are created using the ``phpDocumentor\Descriptor\Builder`` class.
This class uses a, sometimes new, ProjectDescriptor object and assembles an object tree by providing it with an object
or entity representing a file.

For example:

.. code-block:: php
    :linenos:

    <?php
    namespace phpDocumentor\Descriptor;

    // Parse a PHP file using the Reflection component
    $fileReflector = new phpDocumentor\Reflection\File('myFile.php');
    $fileReflector->process();

    // create a new Builder, with a new ProjectDescriptor and add the file to the ProjectDescriptor
    // as a FileDescriptor object
    $builder = new Builder(new ProjectDescriptor());
    $builder->buildFile($fileReflector);

    // get the constructed ProjectDescriptor
    $projectDescriptor = $builder->getProjectDescriptor();

What happens here is that the PHP source code is being interpreted by the Reflection component and a FileReflector
object is created. This is a `Domain Model`_ that encapsulates all underlying Classes, Interfaces, Functions and more.
Because this Object Model is too specialized for Reflection and too heavy for caching and serialization we compile, or
translate, the entire Project to a different Object Model, the Descriptors.

To be able to do this a builder object may accept a FileReflector model and build a FileDescriptor from that. Each
model contained inside the FileReflector is in turn translated to a Descriptor equivalent; this is encapsulated by the
builder and thus not visible from the outside.

Building a Descriptor is a four-step process:

# Assemble the provided input, in this case a Reflector, into a Descriptor
# Filter the Descriptor to remove or transform properties, or even the whole Descriptor
# Validate the Descriptor, a combination or properties may be invalid syntax or elementś may be missing
# Store the Descriptor in a Collection

Assembling a Descriptor
~~~~~~~~~~~~~~~~~~~~~~~

Filtering a Descriptor
~~~~~~~~~~~~~~~~~~~~~~

Validating a Descriptor
~~~~~~~~~~~~~~~~~~~~~~~

Storing a Descriptor
~~~~~~~~~~~~~~~~~~~~

.. _Domain Model: http://martinfowler.com/eaaCatalog/domainModel.html
