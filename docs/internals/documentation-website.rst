Documentation & Website
==================

For phpDocumentor we aim to have a fully automated documentation generation process. This chapter explains how our internal
infrastructure works, and what needs to be done when a new version of phpDocumentor is released.

.. note::

This chapter is focused on administrators.

Setup
-----

Our website runs on a sponsored Kubernetes cluster with a Rancher interface. We are running a single docker image which is
build by the ``Website`` workflow on github, and published to `github packages`_.

The docker image is based on ``nginx:alpine` and contains the assets created by the make target ``build-website``. The image
contains 3 website definitions running on separate ports.

==== =======
Port website
==== =======
80   https://phpdoc.org
81   https://demo.phpdoc.org
82   https://docs.phpdoc.org
==== =======

Ingress
~~~~~~~

The ingress configuration of our website contains a set of rules to map the domains to the correct port. Also the separate
paths are mapped this way.


Deploy process
--------------

The ``Website`` workflow is triggered after all QA checks are passed. Which will build the ``latest`` tag. When finished
the website will automatically update. This means that ``/latest`` path of all domains will be updated using the latest bleeding
edge version of phpDocumentor.

Image tags
----------

At this moment we only publish the ``latest`` tag. When we are releasing a new version of phpDocumentor we need to adjust this
so ``/3.0`` will remain stable using the v3 release.

.. _github packages: https://github.com/phpDocumentor/phpDocumentor/packages/880353

