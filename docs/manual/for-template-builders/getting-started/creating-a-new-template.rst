Creating a new template
=======================

In many applications branding usually means that you create a complete template
from scratch. This gives you full freedom but you need to update your template for
every new release of the application that you are building your template for.

With phpDocumentor this is different. Instead of building a template from scratch you
can 'extend' an existing template and alter those bits that are undesirable.
This provides the power needed to make a template that requires less maintenance
and benefits from any new features introduced in later versions.

Introduction
------------

phpDocumentor uses the **abstract** template as basis for its own templates. This
template can easily be customized and contains a multitude of hooks and
xsl:templates to adapt. This without affecting or duplicating large parts of the
rest of the template.

As such it is advised to use the basis of this template and use CSS to restyle
it to your liking.

Should something prove hard to do, please submit a pull request to the
http://github.com/phpdocumentor/template.abstract repository with a generic
solution or mail your request.

    Please note that the Abstract template intentionally does not use the
    Box model for the layout of the index page. This is needed since iframes
    cannot fluidly fill the remaining height without tables.

    Iframes are a necessity for larger projects to decrease bandwidth and
    increase performance as navigation sidebars can become quite large.

Steps
-----

Depending on the needed level of customization you have the following steps to
go through when creating a template:

1. Generate a template skeleton using the ``template:generate`` method
2. Alter the template.css file in the css folder
3. Extend the index.xsl or api-doc.xsl file with new or overridden xsl:templates
4. Edit the generated template.xml and insert your own values and writers.

That is the gist of it. In the following chapters we will discuss this more
in-depth.

    Generating a template is covered in the `Quickstart`_ and will not be covered
    in the proceeding chapters.