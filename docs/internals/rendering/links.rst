#####
Links
#####

Links are present in various locations of the documentation. To make sure they are represented correctly, there are
various filters and functions in :php:class:`the main twig extension<phpDocumentor\Transformer\Writer\Twig\Extension>`
to help with this.

One of these filters is the ``route`` filter. To this filter you can pass one of the following:

- Descriptors - which will render links to that element in the documentation
- FQSENs - both the object as well as a string, these will render links to that element in the documentation or
  display the FQSEN when no element is included in the rendered docs.
- References and FQSENs coming from Tags, such as the SeeDescriptor tag's Reference.
- URLs - both relative and absolute.
- URLs with the ``doc://`` scheme - these will render references to a document in the Guides; similar to the ``:doc:``
  reference.

Rendering such links is a matter of applying the filter in your twig template like this:

.. code:: twig

   element|route

By default, the generated anchor will attempt to interpret the provided element as a FQSEN and show only the last
part of the FQSEN and decorate the link text with an abbrevation (``ABBR``) tag with the complete FQSEN.

It is also possible to influence the presentation style by providing an extra parameter, this can have the following
options:

* ``normal``
* ``url``
* ``short``
* ``class:short``
* ``file:short``

It is also possible to provide an empty string -representing no formatting- or a text of your choosing, in the latter
scenario this text will be rendered as the link text. This makes it possible to add your own text. An example of this
is the rendering of the description blocks, where the link text of an ``@see`` inline tag is the tag's description
(when available).

.. code:: twig

   element|route('class:short')
