How Descriptors Are Built
=========================

Overview
--------

In this chapter we go into detail on how phpDocumentor builds the Abstract Syntax Tree, also called Descriptors, that
is passed on to the writers. The Abstract Syntax Tree is a simplified representation of the structure of an analyzed
project; it consists of a number of Descriptor objects that each represent an element.

    **Examples of elements are**: The Project itself, Files, Namespaces, Packages, Files, Classes, Interfaces, Methods,
    Arguments, Tags and Types. There are more elements out there, please inspect the Descriptor source folder to get
    an overview.

The Abstract Syntax Tree is, as the name suggests, a hierarchy of Descriptors. As such you can expect the descriptor
for a Method to be a child element of a Class descriptor. This hierarchy tries to follow the logical structure of your
project as much as possible.

In addition to the Abstract Syntax Tree does a projects also contain a number of *indexes*, which are collections of
Descriptors that can match a certain theme. An example of such an index is the 'elements' index, which contains a flat
listing of all Element Descriptors in the Project.

Phases of construction
----------------------

phpDocumentor builds the Abstract Syntax Tree in a number of separate phases:

1. Analyze each file in the project

   1. Parse a PHP File into a Structural Representation
   2. Pass parsed data to the appropriate Assembler
   3. Filter the constructed Descriptor
   4. Validate the constructed Descriptor

Each of these steps are dealt with in separate parts of the application in order to improve performance. In the
subsequent chapters I will go into detail on the process of each step and where in the application flow these happen.

Analyze each file in the project
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This is the most elaborate and CPU intensive task of the application. During this phase the *Parser* will execute a
series of steps that will analyze a file, assemble a Descriptor for each element, filter or alter any unwanted data per
Descriptor and finally validate the Descriptors to see if there are any issues in the original Docblocks.

