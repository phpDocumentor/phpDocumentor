Conventions
===========

In this part of internals documentation we focus on describing as much as possible of how phpDocumentor works. It is
however assumed that you have a firm understanding of the syntax of the language PHP or adjacent language with similar
Object Oriented Principles.

The following subchapters provides more information on conventions and specifics how to read the internal
documentation.

Elements vs. Types
------------------

It is important to note the difference between Elements and Types.

Element
    in the context of phpDocumentor, a structural element of a project which is defined in the source code.

Type
    is a representation of a type of data represented by a variable or other element containing data.

        **Examples**: a Property can contain a variable of the type 'string' or a Method can return a variable of the
        type '\DateTime', *which is a class and hence also an Element*.

    As seen in the example, Types can also refer to Elements.

