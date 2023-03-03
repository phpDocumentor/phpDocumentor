##############
Custom Styling
##############

With the default template, you can add styling in the following ways:

#. Overriding CSS in a ``custom.css.twig`` file
#. Replacing all styles for specific objects and components

.. TODO - write docs on the CSS variables that we use and how you can change styling through those

.. warning::

   We keep BC breaking changes in our templates to a minimum, but we are unable to provide BC guarantees on the HTML
   structure and CSS classes in our templates. We are continuously improving these and to cover them with this
   guarantee would either increase the number of major updates we do, or limit our ability to improve in this area.

Because of the above, if you are using extensive styling changes, we recommend copying the whole `default template`_
into your `.phpdoc/template` folder to prevent breaking changes from occurring. For regular changes, using override as
described in the following sections works splendidly.

.. hint::

   *Where's webpack you may wonder? Or Post-CSS, or any other tool that helps structure CSS.*

   In short, we don't use those. We use Twig templates to provide variables and stitch asset files together.

   This is not because we think this solution is better than webpack or any of the other frontend pipeline tooling, but
   because we cannot assume that developers who run phpDocumentor have node installed on their system. Also, this
   tooling can be picky about the version of node that is used, complicating the matter even further.

***************
CSS Conventions
***************

Before we dive into the how, let's first discuss conventions. We use BEM_ as a naming convention for most of the CSS
elements, and we distinguish between these types of elements:

* Components - (groups of) elements with a specific goal, such as a card.
* Objects - elements that can occur across whole, such as headings, forms, links, etc.

In addition, all CSS classes or phpDocumentor specific attributes start with the ``phpdocumentor`` prefix, or have
``.phpdocumentor`` as top level parent in the selector.

The above is intended to help with mixing CSS files from different sources; if you use Tailwind or Bootstrap in your
projects and want to re-use these, or your own styling, in our templates, we help with that in this way.

*************************
Adding your own overrides
*************************

Adding your own styling is a matter of injecting your own ``custom.css.twig`` template with the CSS changes
that you want.

If you save this file in the folder ``.phpdoc/template/css/base.html.twig`` then phpDocumentor will automatically pick
up on that and add your custom css after running it again.

This file is added at the very end of all other CSS files in phpDocumentor, meaning you can override any CSS selector
that we provide, or add your own. To see which things you can override, you can view the `default template`_ its
source code and look for any file ending in ``.css.twig``. In phpDocumentor, we aim to use a component based
architecture using separate twig files for objects and components; so be sure to check out these folders.

*************************************
Replacing whole objects or components
*************************************

Sometimes, you need the HTML to be different for a bit of styling to work the way you want. Even that is covered by
phpDocumentor.

If you check the objects and components folder in the `default template`_, you can see that we split both the HTML and
the CSS in individual templates. To replace a whole component, you can copy the ``[component].html.twig`` and the
``[component].css.twig`` to your local ``.phpdoc/template`` folder -with the same path as the object you are copying-
and completely rewrite just that component.

Because phpDocumentor uses a Twig trick to "layer" template locations on top of each other, you can replace an original
template file with another by using the exact same file path in your ``.phpdoc/template`` folder as is used in the
`default template`_ its folder structure.

As an example: if you look at our `own template overrides`_, you will find that we override the
``components/header-title.html.twig`` file to render our logo in the top left corner of this very documentation page.

.. _`default template`: https://github.com/phpDocumentor/phpDocumentor/tree/master/data/templates/default
.. _BEM: https://getbem.com/
.. `own template overrides`: https://github.com/phpDocumentor/phpDocumentor/blob/master/.phpdoc/template/
