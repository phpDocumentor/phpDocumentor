########
Compiler
########

The compiler is simple step by step process that takes the parsed AST and transforms it into a
usable form for the transformers. Where the result of the parser is a cachable format the compiler
creates a lot of links between the different elements in the AST. And also transforms the AST into
different structures like UML diagrams, and referenses to external documentation pages.

Each :php:interface:`\phpDocumentor\Compiler\CompilterPassInterface` is responsible for a single
task. These tasks are executed by priority. This make the compiler very flexible and easy to extend.
The diagram below shows the different compiler passes and the order in which they are executed.

