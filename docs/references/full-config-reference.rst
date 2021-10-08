phpdocumentor
=============

Documentation reference

configVersion
-------------

**Default**

.. code-block:: xml
   3

title
-----

Will be the title of your documentation set.

**Default**

.. code-block:: xml
   Documentation

use-cache
---------

By default phpDocumentor uses a lot of caching to speed up re-runs. Disabling this cache will have impact on the resources
consumed by phpDocumentor and the time to complete the rendering. Set this setting to false will disable all cache usage.

**Default**

.. code-block:: xml
   true

paths
-----

Paths can be used to configure the output and cache directory.

.. note::

    By separating the output locations into one for the parser and one for the transformation process it is possible to
    provide a staging location where you indefinitely store your structure file and benefit from the increased speed
    when doing multiple runs. This is called **Incremental Processing** or **Incremental Parsing**.

phpDocumentor automatically uses the cache directory when possible there is way to configure this.

output
^^^^^^

**Default**

.. code-block:: xml
   .phpdoc/build

cache
^^^^^

**Default**

.. code-block:: xml
   .phpdoc/cache

versions
--------

number
~~~~~~

**Default**

.. code-block:: xml
   1.0.0

folder
~~~~~~

**Default**

.. code-block:: xml
  
apis
~~~~

format
``````

The language of your code.

.. note::

   phpDocumentor's internals are prepared to support multiple languages. Right now only php is supported.


**Default**

.. code-block:: xml
   php

visibilities
````````````

Visibilities are setting the deepest level of elements that will be rendered in your documentation. The values can be
combined to fine tune the output. By default phpDocumentor includes all elements unless they are marked as ``@internal``

Api will render only the elements marked as part of your api using the ``@api`` tag. 
Public will filter all public elements from your project and render them in to the documentation.
Protected will filter all public elements from your project and render them in to the documentation.
Private will filter all public elements from your project and render them in to the documentation.

It is possible to configure multiple visibitities. When you combine ``public`` and ``private`` all non internal public and
private elements will be included. If you add ``internal`` also the internal elements will be added.

Adding ``api`` as value will filter all elements that are part of your api.

**Example**

.. code-block:: xml
   <visibilities>public</visibilities>
   <visibilities>protected</visibilities>

default-package-name
````````````````````

When your source code is grouped using the @package tag; what is the name of the default package when none is provided?

**Default**

.. code-block:: xml
   Application

encoding
````````

**Default**

.. code-block:: xml
   utf-8

source
``````

Where should phpDocumentor start looking for your code? In this configration section you can configure the DSN to your
code base. 

The source can be configured using 2 parts. DSN which points to the root of your code base and paths which specify which
folders should be read. Paths can be used to be more specific which folders should be included. All other paths in the root
will be ignored. 

.. note::
   Future versions of phpDocumentor will support other DSN formats rather then just local paths. Right now only
   `file://` is supported

Paths do support glob patterns to be able to include only particular sub directories. 
The example below will only include files in your DSN root directory `src` and it's sub directories, when the file name matches
`*Interface.php`
 
.. code-block:: xml
   <path>/src/**/*Interface.php</path>

.. note::
   The paths in source are relative to the ``dsn``; It is not possible to use absolute paths in a path.

dsn
```

**Default**

.. code-block:: xml
   .

paths
`````

output
``````

**Default**

.. code-block:: xml
   .

ignore
``````

hidden
``````

**Default**

.. code-block:: xml
   true

symlinks
````````

**Default**

.. code-block:: xml
   true

paths
`````

ignore-tags
```````````

extensions
``````````

include-source
``````````````

Should phpDocumentor include your source code in the rendered documentation? By including your source code people reading
your docs can jump directly from the docs to a rendered version of the source code. Which allows them to get more details about
a method or function implementation. 

**Default**

.. code-block:: xml
   true

examples
````````

Examples are code snippets that can be included in your docblocks. This setting will configure where phpDocumentor 
can find them. The paths used in your docblock ``@example`` tags are relative to this dsn.
For more information about ``@example`` please consult the tag reference.

dsn
```

**Default**

.. code-block:: xml
   .

paths
`````

validate
````````

**Default**

.. code-block:: xml
   false

markers
```````

guides
~~~~~~

format
``````

**Default**

.. code-block:: xml
   rst

source
``````

Where should phpDocumentor start looking for your code? In this configuration section you can configure the DSN to your
code base. 

The source can be configured using 2 parts. DSN which points to the root of your code base and paths which specify which
folders should be read. Paths can be used to be more specific which folders should be included. All other paths in the root
will be ignored. 

.. note::
   Future versions of phpDocumentor will support other DSN formats rather then just local paths. Right now only
   `file://` is supported

Paths do support glob patterns to be able to include only particular sub directories. 
The example below will only include files in your DSN root directory ``src`` and it's sub directories, when the file
name matches ``*Interface.php``
 
.. code-block:: xml
   <path>/src/**/*Interface.php</path>

.. note::
   The paths in source are relative to the ``dsn``; It is not possible to use absolute paths in a path.

dsn
```

**Default**

.. code-block:: xml
   .

paths
`````

output
``````

**Default**

.. code-block:: xml
   docs

settings
--------

Some settings are not part of the normal configuration parts of phpDocumentor. Because there have impact 
on the full application behavior, or enable experimental parts in phpDocumentor. 

Settings are key-value configuration options, which are listed with the following command:

.. code-block:: shell-session
   $ phpdoc --list-settings

name
~~~~

value
~~~~~

templates
---------

name
~~~~

**Default**

.. code-block:: xml
   default

location
~~~~~~~~

parameters
~~~~~~~~~~

name
````

value
`````
