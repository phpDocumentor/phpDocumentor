##########
Extensions
##########

.. warning::

    This feature is still under development and only available in the nightly docker builds.

**********
Installing
**********

PhpDocumentor can be extended with additional functionality by installing extensions. By default the application
will search for an extensions folder in the ``.phpdoc`` folder in your working directory.

Supported extension styles:

- folder

Once extensions are correctly loaded phpDocumentor will print a message in the console:

    Loaded extensions:
    [OK] phpdocumentor/directives:1.0.0

    Failed to load extensions:
    [WARNING] phpdocumentor/invalid:1.0.0

Extensions are validated before they are acually loaded. If an extension is invalid it will not be loaded and a warning
will be printed in the console. Like in the example above. To load an extension it must be valid and compatible with
the current version of phpDocumentor. Extension developers must specify the phpDocumentor version they are compatible
with in the manifest file.

************************
Write your own extension
************************

To write your own extension you need to create a folder in the extensions folder with the name of your extension.
In this folder you need to create a manifest file called ``manifest.xml``. This file contains the information:

.. code-block:: xml

    <?xml version="1.0" encoding="utf-8" ?>
    <phar xmlns="https://phar.io/xml/manifest/1.0">
        <contains name="phpDocumentor\Example" version="1.0" type="extension">
            <extension for="phpdocumentor/phpdocumentor" compatible="^3.5"/>
        </contains>

        <copyright>
            <author name="You" email="you@example.com"/>
            <license type="MIT" url="https://github.com/phpdocumentor/phpdocumentor-example-extension/blob/1.0.0/LICENSE"/>
        </copyright>

        <requires>
            <php version="^8.1"/>
        </requires>

        <bundles>
            <component name="phpDocumentor\Example\Extension" version="1.0" />
        </bundles>
    </phar>

The ``bundles`` section contains the class that should be loaded when the extension is loaded. This class must extend
:php:class:`phpDocumentor\Extension\Extension`. And should not contain any other logic rather than registering services.

.. hint::

   Under the hood PhpDocumentor uses the Symfony Dependency Injection component. You can find more information about
   this component in the `Symfony Dependency Injection documentation <https://symfony.com/doc/current/components/dependency_injection.html>`_.
   This also shows how to register services. We are using the DI extensions of Symfony to register services. Like bundles
   do in a Symfony application.

Autoloading
-----------

The directory of the extension is added to the autoloader. So you can use the autoloader to load classes in your
extension. Please note that the autoloader is not accessible by the extension itself. So you cannot add classes yourself
to the autoloader. This is done by phpDocumentor following the PSR-4 standard. The root namespace is the name of the
extension. So in the example above the root namespace is ``phpDocumentor\Example``.

By using the same autoloader as the application itself you have access to all classes that are loaded by the application.
Services that are registered in the application can be used in the extension.

Extension points
----------------

phpDocumentor does not have real extension points. But we have defined some common locations that can be used to extend
the application. Depending on the type of extension you are writing you can use them.

