##################
Changing the Color
##################

Don't like green and need a different color to match your own site's theme? We got you covered!

To change the color scheme you can do that from the command line or from configuration using phpDocumentor's
settings.

************
Command-line
************

To do that from the command line, you can add the following option:

.. code::

   -s template.color=[color name]

*************
Configuration
*************

Or in the configuration by adding the following directive:

.. code::

	<?xml version="1.0" encoding="UTF-8" ?>
	<phpdocumentor ...>
		...
	   <setting name="template.color" value="[color name]"/>
		...
	</phpdocumentor>

***********
Color names
***********

phpDocumentor supports the following color schemes:

**Material Design colors**

- red
- pink
- purple
- deep-purple
- indigo
- blue
- light-blue
- cyan
- teal
- green
- light-green
- lime
- yellow
- amber
- orange
- deep-orange
- brown

**Custom colors**

- phpdocumentor-green
