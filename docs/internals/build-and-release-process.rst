Build and Release process
=========================

.. note::

   The process described in this document is not yet fully implemented but is a specification to which the Build and
   Release process should adhere.

Introduction
------------

Releasing phpDocumentor involves a fair amount of actions due to the number of installation options and involved third-
parties. This process is completely automated and follows the basic principles of Continuous Delivery where with each
commit to the master branch may lead to a new release.

.. note::

   the term 'Deployment' is explicitly avoided given that the Build and Release process is responsible for creating
   binaries that may be deployed by users and not by ourselves.

Despite that each commit to master should be releasable it does not mean that every commit should be pushed to the
production pear channel.

Pipeline
--------

#. Commit phase

   #. Clone the release-branch into a deployment folder
   #. Run a composer install command with the arguments `--no-dev --optimize-autoloader --prefer-dist`
   #. Run Unit tests of the deployment folder
   #. Run Behat tests of the deployment folder

#. Analysis phase

   #. Run phpCodeSniffer and aggregate results

#. Packaging phase

   #. Build PEAR package
   #. Build PHAR package
   #. Build documentation
   #. Store PEAR and PHAR packages in artifact repository

#. Release phase

   #. Commit generated artifacts (package.xml) <<< can this be removed??
   #. Deploy PEAR package to channel
   #. Upload PHAR to phpdoc.org/phpDocumentor.phar
   #. Upload PHAR to phpdoc.org/archives/phpDocumentor-[version].phar
   #. Create new issue with https://github.com/josegonzalez/homebrew-php using the SHA1 of the phar file and the version
      number to request an update of the homebrew files (see https://github.com/phpDocumentor/phpDocumentor2/issues/909)
   #. Upload documentation to http://www.phpdoc.org/docs/[major].[minor]
   #. Set Symlink for latest documentation (http://www.phpdoc.org/docs/latest) to http://www.phpdoc.org/docs/[major].[minor]

#. Announcements

   #. Send a mail to the mailing list with the change log contents and title
   #. Send a tweet with the new release and a link to the Github release information
   #. Update release information on github with:

      #. Release title (taken from changelog)
      #. Changelog
      #. PEAR archive file
      #. Phar archive file
