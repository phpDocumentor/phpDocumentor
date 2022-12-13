##########################
Adding your own navigation
##########################

With the default template, adding your own navigation is a matter of injecting your own ``base.html.twig`` template
and setting a twig variable ``topMenu`` in it.

As an example, let's take our own navigation definition:

.. code:: twig

   {% extends 'layout.html.twig' %}

   {%
   set topMenu = {
       "menu": [
           { "name": "About", "url": "https://phpdoc.org/3.0/"},
           { "name": "Documentation", "url": "https://docs.phpdoc.org/3.0/"},
       ],
       "social": [
           { "iconClass": "fab fa-twitter", "url": "https://twitter.com/phpdocumentor"},
           { "iconClass": "fab fa-github", "url": "https://github.com/phpdocumentor/phpdocumentor"},
           { "iconClass": "fas fa-envelope-open-text", "url": "https://groups.google.com/forum/#!forum/phpdocumentor"}
       ]
   }
   %}

If you save this file in the folder ``.phpdoc/template/base.html.twig`` then phpDocumentor will automatically pick up on
that and render these menu items in the correct location.

.. TODO - write docs on injecting your own templates to go more in-depth on this.

Let's take a deeper look at the structure now; the topMenu variable can contain any number of sections in it and the key
of each section is also used as a css class.

.. note::

   For now, just know that 'menu' and 'social' are named sections where the 'social' section
   will align to the right.

.. TODO - write a chapter way more down to explain that these keys are transformed into CSS that you can modify

Each section is a list of menu items the following fields

url
    The absolute url to the location where you want the link to direct your audience towards.
name (optional)
    The name of the menu item, which is shown for readers to click on. May be omitted when the field ``iconClass``
    is used.
iconClass (optional)
    When you want to show an icon instead of a name, use this field and provide the CSS classes used to render an
    icon at this location. By default, we use the `FontAwesome`_ 5 iconset and you can use any of these icons.

A straightforward menu could look like this:

.. code:: twig

   {% extends 'layout.html.twig' %}

   {%
   set topMenu = {
       "menu": [
           { "name": "About", "url": "https://my-awesome-company/about/"},
           { "name": "Documentation", "url": "https://my-awesome-company/docs/"},
           { "name": "Contact", "url": "https://my-awesome-company/contact/"},
       ]
   }
   %}

.. _FontAwesome: https://fontawesome.com/v5/search
