Configuration
=============

DocBlox is meant as a highly configurable and extensible application. As such
there are a lot of things that can be configured by the user.

An overview will be given in this chapter where you could place the configuration
file and what it needs to look like.

Location
--------

The easiest solution would be to place the configuration file in the root of
your project with the name: `docblox.dist.xml`. This file can be committed to
a Revision Control System and thus will the settings be always available.

When the action in the previous paragraph is executed then you do not need to
provide the location of the configuration file to DocBlox; the following command
suffices to build your documentation:

    $ docblox

An additional benefit is that it is possible for each developer to place a file
called `docblox.xml` in their project, in addition to the `docblox.dist.xml`.

This way developers can provide their own settings and if you tell your source
code repository to ignore this file then their local modifications will not
interfere.

**Note:** _the file `docblox.xml` is used instead of `docblox.dist.xml`
and thus does not supplement it_

Another option is to use the `-c` or `--configuration` arguments to tell DocBlox
the location of your configuration file. This can be convenient for centralized
configuration management or using different settings per environment.

Basic configuration
-------------------

DocBlox follows the _convention over configuration_ style and as such it is only
necessary to specify the options which you want to change with regard to the
defaults.

The easiest way to find out what the defaults are is to look in the
configuration template, which is located in _[DOCBLOX_FOLDER]/data/docblox.tpl.xml_
or to examine the specifications in this document.

Usually the following configuration suffices for your project:

    <?xml version="1.0" encoding="UTF-8" ?>
    <docblox>
      <parser>
        <target>data/output</target>
      </parser>
      <transformer>
        <target>data/output</target>
      </transformer>
      <files>
        <directory>.</directory>
      </files>
    </docblox>

_Remember when we told you about there being a parser and transformer?_ The
configuration expects you to specify for both what their output / target folder
should be.

> This way it is possible to provide a staging location where you indefinitely
> store your structure file and benefit from the increased speed when doing
> multiple runs. This is called `Incremental Processing` or `Incremental Parsing`.

The transformer expects the source file to be at the target location of the
parser so you need not specify that explicitly.

The files section allows you to specify where the source code for your project is.

      <files>
        <directory>.</directory>
      </files>

It is allowed to use relative paths here; just remember that these are relative
from your execution folder.

Transformations
----------------------

