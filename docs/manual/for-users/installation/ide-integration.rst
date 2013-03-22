IDE Integration
===============

FIXME: the images in this chapter are outdated and contain DocBlox references.
These should be replaces and this document split into sub-documents per IDE.

Als support for Sublime Text, Vim and Eclipse should be added.

phpStorm
--------

1. Open settings and search for *External Tools*.

   .. image:: /.static/for-users/installation/ide-integration/phpstorm-1.png

2. Click on *Add*.
3. Enter the data as shown on the screenshot. You can change the parameters as
   required. And by adding the Project's source folder as 'Working Directory' will
   phpDocumentor automatically pick up your configuration file if you have it.

   .. image:: /.static/for-users/installation/ide-integration/phpstorm-2.png

4. Click on *OK*.
5. Click again on *OK* to close your settings screen.
6. You can now generate your documentation by in your *Project* view by right
   clicking on any file or folder, selecting *QA* and then *phpDocumentor*.

   .. image:: /.static/for-users/installation/ide-integration/phpstorm-3.png

Netbeans
--------

1. Open your *Tools > Options* menu.

   .. image:: /.static/for-users/installation/ide-integration/netbeans-1.png

2. Click on the PHP icons to go to the PHP settings.
3. Open the PhpDoc tab and replace */usr/bin/phpdoc* with the location of your
   phpDocumentor executable (commonly */usr/bin/phpdoc*). It is here that you can
   enter additional command line arguments.

   .. image:: /.static/for-users/installation/ide-integration/netbeans-2.png

4. Click on *OK* to close the Options screen.
5. Right click on your project name and select *Generate PhpDoc* to let Netbeans
   invoke phpDocumentor and generate your documentation.

   .. image:: /.static/for-users/installation/ide-integration/netbeans-3.png

6. After phpDocumentor finishes will NetBeans open your documentation in your browser.