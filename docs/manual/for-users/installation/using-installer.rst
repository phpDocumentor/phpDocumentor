Using the installer
===================

phpDocumentor provides a stand alone installer that will perform most of the
actions necessary for a smooth installation.

The steps for a manual installation are:

1. Download the installer from
   https://raw.github.com/phpDocumentor/phpDocumentor2/develop/installer.php
   to the intended location. We will refer to the intended location as <PHPDOC\_PATH>.
2. Run the installer::

       php installer.php

.. tip::

   If you want to be able to use the phpdoc command from any location on your
   computer you can do the following:

   **For Linux or Mac OSX**
       Create a symbolic link from <PHPDOC\_PATH>/bin/phpdoc.php to the folder where
       your binaries are housed, usually /usr/bin or /usr/local/bin, named
       ``phpdoc``::

           ln -s <PHPDOC\_PATH>/bin/phpdoc.php /user/bin/phpdoc

   **For Windows**
       Add <PHPDOC\_PATH>/bin to your PATH so that you can invoke ``phpdoc.bat``
       from any location.

       `Don't know how? Microsoft can tell you <http://answers.microsoft.com/en-us/windows/forum/windows_vista-files/how-do-i-change-the-system-path-permanently/8bbf70be-4671-4cce-9122-52787c77865d>`_

