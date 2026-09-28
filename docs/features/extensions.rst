##########
Extensions
##########

PhpDocumentor can be extended with additional functionality by installing extensions. By default the application
will search for an ``extensions``` folder in the ``.phpdoc`` folder in your working directory.

Supported extension styles:

- folder

Once extensions are correctly loaded phpDocumentor will print a message in the console:

    Loaded extensions:
    [OK] phpdocumentor/directives:1.0.0

    Failed to load extensions:
    [WARNING] phpdocumentor/invalid:1.0.0

Extensions are validated before they are actually loaded. If an extension is invalid it will not be loaded and a warning
will be printed in the console. Like in the example above. To load an extension it must be valid and compatible with
the current version of phpDocumentor. Extension developers must specify the phpDocumentor version they are compatible
with in the manifest file.

Want to write your own extension? Head over to :doc:`the extensions guide </guides/extensions/index>` to learn how
to set one up, and how to use it to add things like :doc:`Twig extensions </guides/extensions/twig-extension>` or
:doc:`custom RestructuredText directives </guides/extensions/custom-directive>`.
