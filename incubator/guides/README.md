# Guides

> **EXPERIMENTAL**: Anything regarding Guide generation, in this folder or elsewhere, is to be considered experimental
> and prone to be changed or removed without notice or consideration for BC.

phpDocumentor's Guides library takes hand-written documentation in code repositories, creates an AST from that and feeds
it to a renderer to create the desired output.

As part of this goal, the Guides library itself is more of a framework where you can plug in support for an input 
format, such as Restructured Text, and plug in an output format to output towards, such as HTML.

## Supported Formats

### Input

> As this is a new component, the number of formats are limited and can expand in the future

- RestructuredText; Well-supported, though still work in progress
- Markdown; Early stages, not working yet

### Output

> As this is a new component, the number of formats are limited and can expand in the future

- HTML
- LaTeX; Well-supported, though a work in progress

## TODO: Separating the Guides library into input format packages

Separating the Guides library into separate packages has recently been started, in this package there is still overlap
with the Restructured Text add-on package and needs to be addressed. The following pieces of code need to be refactored
to be input format agnostic:

1. Table(Node)
2. Span(Node)

## TODO: Extracting the output formats

The output formats / rendering is currently still inside this package and the twig templates are in phpDocumentor's 
main package. Twig is expected to still be the core method for creating the output, but we would like to extract the
individual formats (HTML, LaTeX, etc.) into separate packages.
