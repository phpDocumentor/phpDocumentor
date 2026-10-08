########
Compiler
########

The compiler is the step in phpDocumentor that prepares the parsed project model for transformation.
It takes the descriptors produced by the parsing stage and turns them into a more connected and usable
form for the rest of the application.

At a high level, the compiler links related elements together and derives additional structure that the
transformers can use later. This is where phpDocumentor starts to work with the project as a whole
rather than as individual parsed files.

The compiler is built from small compiler passes. Each pass is responsible for one task, and the passes
are executed in priority order. This keeps the compiler flexible and makes it easy to extend without
changing the overall structure.

The diagram below shows the compiler passes and the order in which they are executed.
