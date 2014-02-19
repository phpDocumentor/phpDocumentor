Glossary
========

.. glossary::

    DocComment
    DocComments
        A DocComment starts with a forward slash and two asterisks (``/**``), which is similar to how you start a
        multiline comment but with an additional asterisk, and ends with an asterisk and forward slash (``*/``).
        DocComments may be a single line in size but may also span multiple lines, in which case each line must start
        with an asterisk. It is customary, and recommended, to align the asterisks vertically when spanning multiple
        lines.

        So, a single line DocComment looks like this::

            /** This is a single line DocComment. */

        And a multiline DocComment looks like this::

            /**
             * This is a multi-line DocComment.
             */

    DocBlock
    DocBlocks
        This is a :term:`DocComment` containing a single :term:`PHPDoc` and
        represents the basic in-source representation.

        Example:

        .. code-block:: php
           :linenos:

            /**
             * Returns the name of this object.
             *
             * @return string
             */

    AST
    Abstract Syntax Tree
    Abstract Syntax Tree (AST)
        phpDocumentor generates a XML file between parsing your source code and
        generating the HTML output. This structure file (called *structure.xml*)
        contains the raw analyzed data of your project, also called: an Abstract
        Syntax Tree.

        This same file is also used by phpDocumentor to do incremental parsing
        of your project by comparing the contents of this file with the content
        on disk.

        It is thus recommended to keep your structure file and allow
        phpDocumentor to re-use the contained information.

    PHPDoc
        This is a section of documentation which provides information on several
        aspects of :term:`Structural Elements`.

        A :term:`PHPDoc` is usually enveloped in a :term:`DocComment`.

        Example:

        .. code-block:: php
           :linenos:

             Returns the name of this object.

             @return string

        Example enveloped in a :term:`DocComment`:

        .. code-block:: php
           :linenos:

            /**
             * Returns the name of this object.
             *
             * @return string
             */

    DocComment
        This is a special type of comment which starts with `/**`, ends
        with `*/` and may contain any number of lines in between. Every line
        should start with an asterisk, which is aligned with the first asterisk
        of the opening clause.

        Single line example:

        .. code-block:: php
           :linenos:

            /** <...> */

        Multiline example:

        .. code-block:: php
           :linenos:

            /**
             * <...>
             */

    Structural Element
    Structural Elements
        This is a collection of Programming Constructs which SHOULD be
        preceded by a :term:`DocBlock`. The collection contains the following
        constructs:

        * namespace
        * require(_once)
        * include(_once)
        * class
        * interface
        * trait
        * function (including methods)
        * property
        * constant

        It is RECOMMENDED to precede :term:`Structural Elements` with a
        :term:`DocBlock` at its definition and not with each individual usage.

        Example:

        .. code-block:: php
           :linenos:

            /** @var int This is a counter. */
            $int = 0;

            // there should be no docblock here
            $int++;

        Or:

        .. code-block:: php
           :linenos:

            /**
             * This class acts as an example on where to position a DocBlock.
             */
            class Foo
            {
                /** @var string|null Should contain a description if available */
                protected $description = null;

                /**
                 * This method sets a description.
                 *
                 * @param string $description A text with a maximum of 80 characters.
                 *
                 * @return void
                 */
                public function setDescription($description)
                {
                    // there should be no docblock here
                    $this->description = $description;
                }
            }

        Another example is to document the variable in a foreach explicitly; many IDEs
        use this information to help you with auto-completion:

        .. code-block:: php
           :linenos:

            /** @var \Sqlite3 $sqlite */
            foreach($connections as $sqlite) {
                // there should be no docblock here
                $sqlite->open('/my/database/path');
                <...>
            }

    Type
        This is a generic name for anything that can be returned or provided as
        identity for a value.

        It is recommended to read the chapter :doc:`references/phpdoc/types` for a
        detailed description.

    FQSEN
        See term:`Fully Qualified Structural Element Name (FQSEN)`

    Fully Qualified Structural Element Name (FQSEN)
       Each documentable element can be referenced using a unique name based on
       its local name and any containers it is in.

       It is best demonstrated using an example:

           \\My\\Space\\MyClass::myMethod()

       This FQSEN identifies the *myMethod* method that is contained in the
       *MyClass* class, which in turn is contained inside the *My\\Space*
       namespace.

    Template
    Templates
       .. note:: Here a text must be added

    Transformation
    Transformations
       .. note:: Here a text must be added

    Summary
       .. note:: Here a text must be added

    Description
       .. note:: Here a text must be added

    Tag
    Tags
       .. note:: Here a text must be added

    Inline Tag
    Inline Tags
       .. note:: Here a text must be added

    Annotation
    Annotations
       .. note:: Here a text must be added

