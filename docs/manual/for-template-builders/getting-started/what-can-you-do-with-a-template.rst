What can you do with a template?
================================

Without a template you would only have a XML file (the Abstract Syntax Tree, or
AST) with an analysis of your source.

It is the template that helps to transform all that raw data into an attractive
presentation or the machine-readable format of your tool of preference.

With templates you can:

* Convert the Abstract Syntax Tree into HTML using XSLT
* Convert the Abstract Syntax Tree into HTML using Twig
* Copy stylesheets, images and javascript files from your template to the
  target location.
* Generate a Class Diagram
* Convert the errors in the Abstract Syntax Tree into a checkstyle formatted
  error report
* And more ..

You could even include static files in your template and copy those to the
intended destination.

But there is more! Templates can access files in eachother, which enables you
to extend an existing template with copying the entire thing.

Given a little creativity there are even more ways to influence the creation of
your presentation.