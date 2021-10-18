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
- LaTeX; Well-supported, though still work in progress

## Usage

> **Note**: as this library is still under development, these instructions may change before release

You can use this library in 2 ways:

1. Parsing a (series of) markup-based documents into an AST
2. Rendering the aforementioned AST into, for example, HTML documents.

This split is by design to allow for consumers to implement caching mechanism, implement their own rendering pipelines
and other nifty business.

### Parsing

With Guides' parser, you can convert a markup language's text into an AST. This AST can be used in other tooling to
interpret it or reformat it, or it can be passed through Guides' Renderer to generate rendered documentation from it.

#### Step 1. Install Language Packages

On its own, Guides' Parser does not know which and what kind of language to parse. To use it, you need to install a 
language-specific package as well; such as `guides-restructuredtext` for Restructured Text, or `guides-markdown` for 
CommonMark-based Markdown.

#### Step 2. Instantiate the Parser

``` php
// Define which language conversions are supported by Guides
$markupParsers = [
    new \phpDocumentor\Guides\RestructuredText\MarkupLanguageParser(...),
];

// Define which output formats are supported by Guides
$outputFormats = new OutputFormats(
    [
        new \phpDocumentor\Guides\RestructuredText\HTML\HTMLFormat(...),
    ]
);

// Declare how URLs are generated
$urlGenerator = new \phpDocumentor\Guides\UrlGenerator();

// Instantate the Parser itself, optionally with a PSR-3 compliant logger at $logger.
$parser = new Parser($urlGenerator, $outputFormats, $markupParsers, $logger);
```

#### Step 3. Prepare for parsing multiple files (optional)

When you want to parse a series of files that go together or have references to assets or other external files; you need
to prepare the parser for each file.

> **Note**: This step can be skipped if you only want to parse a single standalone block of text. If you omit this 
> step it will be implicitly done by the `parse()` method with the current working directory, no source and destination
> path; and index as filename.

``` php
// A Table of Contents-like collection; only need to instantiated once for all files.
// This collection will be populated by the Parser with metadata derived from the parsed file.
$metas = new Metas();

// The filesystem to load/include files from, can be any FlySystem v1 
// Adapter: https://flysystem.thephpleague.com/v1/docs/
$filesystem = new Filesystem(new Local('/home/myUser/projects/my/project'));

// Path of this file in the filesystem, helps to calculate relative source paths from; 
// i.e. /home/myUser/projects/my/project/docs
$sourcePath = 'docs';  

// Path of this file on the destination location, helps to calculate relative destination paths; 
// i.e. https://docs.myproject.com/latest 
$destinationPath = 'latest'; 

// The current filename, important: omit the extension!
$fileName = 'index';

// DO IT!
$parser->prepare($metas, $filesystem, $sourcePath, $destinationPath, $fileName);
```

#### Step 4. Profit! Parse the file contents

Once you are ready, just pass the markup text that you want parse and which format it is in (or omit that if the format
is `rst`).

The parse method will then start to interpret the given text and return an instance of 
`\phpDocumentor\Guides\Nodes\DocumentNode` with  

``` php
$text = 'My *Awesome* file';

// What format is this file is? (default: "rst")
$inputFormat = 'rst';

$document = $parser->parse($text, $inputFormat);
```

## TODOs

As this library is under development, it currently still has dependencies on the phpDocumentor main package or between
the parser and the renderer. There are multiple TODO statements in the code that need to be resolved before this package 
can be promoted to an independent library.

### Separating the Guides library into input format packages

Separating the Guides library into separate packages has recently been started, in this package there is still overlap
with the Restructured Text add-on package and needs to be addressed. The following pieces of code need to be refactored
to be input format agnostic:

1. Table(Node)
2. Span(Node)

### Extracting the output formats

The output formats / rendering is currently still inside this package and the twig templates are in phpDocumentor's 
main package. Twig is expected to still be the core method for creating the output, but we would like to extract the
individual formats (HTML, LaTeX, etc.) into separate packages.
